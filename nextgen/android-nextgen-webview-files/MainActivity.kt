package your.package.name

import android.Manifest
import android.app.Activity
import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.widget.Toast
import android.webkit.CookieManager
import android.webkit.ValueCallback
import android.webkit.WebChromeClient
import android.webkit.GeolocationPermissions
import android.webkit.PermissionRequest
import android.webkit.WebResourceRequest
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import androidx.activity.OnBackPressedCallback
import androidx.appcompat.app.AppCompatActivity

class MainActivity : AppCompatActivity() {

    private lateinit var webView: WebView
    private var filePathCallback: ValueCallback<Array<Uri>>? = null
    private val FILE_CHOOSER_REQUEST_CODE = 1001

    private val prefsName = "nextgen_prefs"
    private val keyUrl = "base_url"

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        val prefs = getSharedPreferences(prefsName, MODE_PRIVATE)
        val baseUrl = prefs.getString(keyUrl, null)

        if (baseUrl.isNullOrBlank()) {
            startActivity(Intent(this, UrlActivity::class.java))
            finish()
            return
        }

        val baseRoot = normalizeNextGenRoot(baseUrl)
        val baseHost = Uri.parse(baseRoot).host
        val startUrl = baseRoot + "view/livraison.php"

        webView = findViewById(R.id.webView)

        val s = webView.settings
        s.javaScriptEnabled = true
        s.domStorageEnabled = true
        s.databaseEnabled = true
        s.allowFileAccess = true
        s.allowContentAccess = true
        s.javaScriptCanOpenWindowsAutomatically = true
        s.setSupportMultipleWindows(true)
        s.cacheMode = WebSettings.LOAD_DEFAULT

        // ✅ ADD THESE NEW MOBILE-OPTIMIZED SETTINGS:
        s.useWideViewPort = true                    // Enable viewport
        s.loadWithOverviewMode = true              // Load page to fit screen width
        s.setSupportZoom(true)                     // Allow zoom
        s.builtInZoomControls = true               // Enable zoom controls
        s.displayZoomControls = false              // Hide zoom buttons (use pinch)
        s.layoutAlgorithm = WebSettings.LayoutAlgorithm.TEXT_AUTOSIZING  // Auto-size text
        s.textZoom = 100                           // Default text size (adjust 80-120)

        // Performance optimizations
        @Suppress("DEPRECATION")
        s.setRenderPriority(WebSettings.RenderPriority.HIGH)
        s.mixedContentMode = WebSettings.MIXED_CONTENT_ALWAYS_ALLOW  // Allow HTTP in HTTPS

        val cm = CookieManager.getInstance()
        cm.setAcceptCookie(true)
        cm.setAcceptThirdPartyCookies(webView, true)

        webView.webViewClient = object : WebViewClient() {
            override fun shouldOverrideUrlLoading(view: WebView, request: WebResourceRequest): Boolean {
                val url = request.url
                val urlStr = url.toString()

                // Allow external https links (CDNs, map tiles, etc.)
                if (url.host != null && url.host != baseHost) {
                    // Let WebView handle it normally (most are subresource requests; this is for user clicks)
                    if (url.scheme == "https") {
                        view.loadUrl(urlStr)
                    } else {
                        try {
                            startActivity(Intent(Intent.ACTION_VIEW, url))
                        } catch (_: Exception) {
                        }
                    }
                    return true
                }

                // Same host: keep the app inside Delivery-only routes
                if (isAllowedDeliveryUrl(url, baseRoot)) {
                    view.loadUrl(urlStr)
                } else {
                    Toast.makeText(this@MainActivity, "Delivery app: section not available", Toast.LENGTH_SHORT).show()
                    view.loadUrl(startUrl)
                }
                return true
            }
        }

        webView.webChromeClient = object : WebChromeClient() {
            override fun onGeolocationPermissionsShowPrompt(origin: String?, callback: GeolocationPermissions.Callback?) {
                requestPermissions(arrayOf(Manifest.permission.ACCESS_FINE_LOCATION, Manifest.permission.ACCESS_COARSE_LOCATION), 0)
                callback?.invoke(origin, true, false)
            }
            override fun onPermissionRequest(request: PermissionRequest?) {
                request?.grant(request.resources)
            }
            override fun onShowFileChooser(
                webView: WebView?,
                filePathCallback: ValueCallback<Array<Uri>>?,
                fileChooserParams: FileChooserParams?
            ): Boolean {
                this@MainActivity.filePathCallback?.onReceiveValue(null)
                this@MainActivity.filePathCallback = filePathCallback

                val intent = fileChooserParams?.createIntent()
                return try {
                    startActivityForResult(intent, FILE_CHOOSER_REQUEST_CODE)
                    true
                } catch (e: Exception) {
                    this@MainActivity.filePathCallback = null
                    false
                }
            }
        }

        webView.loadUrl(startUrl)

        onBackPressedDispatcher.addCallback(this, object : OnBackPressedCallback(true) {
            override fun handleOnBackPressed() {
                if (webView.canGoBack()) webView.goBack() else finish()
            }
        })

        requestPermissions(arrayOf(Manifest.permission.RECORD_AUDIO, Manifest.permission.CAMERA), 1)
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)

        if (requestCode == FILE_CHOOSER_REQUEST_CODE) {
            val result: Array<Uri>? =
                if (resultCode == Activity.RESULT_OK && data != null) {
                    val uri = data.data
                    if (uri != null) arrayOf(uri) else null
                } else null

            filePathCallback?.onReceiveValue(result)
            filePathCallback = null
        }
    }

    private fun normalizeNextGenRoot(input: String): String {
        var url = input.trim()
        val idx = url.indexOf("/nextgen/")
        if (idx >= 0) {
            url = url.substring(0, idx + "/nextgen/".length)
        }
        if (!url.endsWith("/")) url += "/"
        return url
    }

    private fun isAllowedDeliveryUrl(url: Uri, baseRoot: String): Boolean {
        val normalizedRoot = normalizeNextGenRoot(baseRoot)
        val full = url.toString()
        if (!full.startsWith(normalizedRoot)) return false

        // Allow the delivery entry and its views
        if (full.startsWith(normalizedRoot + "view/livraison.php")) return true
        if (full.startsWith(normalizedRoot + "view/livraison_gaming.php")) return true
        if (full.startsWith(normalizedRoot + "view/livraison_tracking.php")) return true
        if (full.startsWith(normalizedRoot + "view/tracking.php")) return true

        // Allow trajet tracking APIs
        if (full.startsWith(normalizedRoot + "api/trajet.php")) return true
        if (full.startsWith(normalizedRoot + "view/api/trajet.php")) return true

        // Allow static assets needed by delivery pages
        if (full.startsWith(normalizedRoot + "public/")) return true
        if (full.startsWith(normalizedRoot + "resources/")) return true

        return false
    }
}
