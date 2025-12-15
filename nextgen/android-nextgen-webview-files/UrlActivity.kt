package your.package.name

import android.content.Intent
import android.os.Bundle
import android.widget.Button
import android.widget.EditText
import androidx.appcompat.app.AppCompatActivity

class UrlActivity : AppCompatActivity() {

    private val prefsName = "nextgen_prefs"
    private val keyUrl = "base_url"

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_url)

        val etUrl = findViewById<EditText>(R.id.etUrl)
        val btnOpen = findViewById<Button>(R.id.btnOpen)
        val btnClear = findViewById<Button>(R.id.btnClear)

        val prefs = getSharedPreferences(prefsName, MODE_PRIVATE)
        val saved = prefs.getString(keyUrl, "") ?: ""
        if (saved.isNotBlank()) {
            etUrl.setText(saved)
        }

        btnOpen.setOnClickListener {
            val raw = etUrl.text.toString().trim()
            val url = normalizeNextGenRoot(raw)

            if (!url.startsWith("https://")) {
                etUrl.error = "Use the https:// ngrok URL"
                return@setOnClickListener
            }

            if (!url.endsWith("/nextgen/")) {
                etUrl.error = "URL must end with /nextgen/"
                return@setOnClickListener
            }

            prefs.edit().putString(keyUrl, url).apply()
            startActivity(Intent(this, MainActivity::class.java))
        }

        btnClear.setOnClickListener {
            prefs.edit().remove(keyUrl).apply()
            etUrl.setText("")
        }
    }

    private fun normalizeNextGenRoot(input: String): String {
        var url = input.trim()
        // Strip any deep link inside nextgen and keep only .../nextgen/
        val idx = url.indexOf("/nextgen/")
        if (idx >= 0) {
            url = url.substring(0, idx + "/nextgen/".length)
        }
        if (!url.endsWith("/")) url += "/"
        return url
    }
}
