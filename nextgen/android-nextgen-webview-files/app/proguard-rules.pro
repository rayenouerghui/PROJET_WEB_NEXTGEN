# Keep WebView and JS interfaces
-keepclassmembers class * {
    @android.webkit.JavascriptInterface <methods>;
}
