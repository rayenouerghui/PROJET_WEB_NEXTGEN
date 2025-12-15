# NextGen Android WebView file pack

This folder contains a **copy/paste-ready** set of Android files (Kotlin + XML) to build an APK that loads the **Delivery module only** (Livraison + Trajet) through a WebView (works with ngrok).

## What you get
- `AndroidManifest.xml` (INTERNET permission + UrlActivity launcher)
- `UrlActivity.kt` (save ngrok URL; app will start directly on Delivery page)
- `MainActivity.kt` (WebView with JS + cookies + upload picker + delivery-only navigation guard)
- `activity_url.xml` (URL screen)
- `activity_main.xml` (WebView screen)

## How to use (Android Studio)
1. Android Studio → New Project → **Empty Views Activity** (Kotlin)
2. After project is created, copy these files into your Android project:

### Replace these paths
- `app/src/main/AndroidManifest.xml`  ← from this folder
- `app/src/main/java/<your_package>/UrlActivity.kt`
- `app/src/main/java/<your_package>/MainActivity.kt`
- `app/src/main/res/layout/activity_url.xml`
- `app/src/main/res/layout/activity_main.xml`

3. In both Kotlin files, replace the first line package:

`package your.package.name`

with your real package name (shown at the top of `MainActivity.kt` created by Android Studio).

4. Run XAMPP (Apache + MySQL), then run:

`ngrok http 80`

5. Install/run the app, paste your URL (must end with `/nextgen/`):

`https://XXXX.ngrok-free.app/user+produit+reclamation+laivrasion+evenment+blog/nextgen/`

The app will automatically open:

`.../nextgen/view/livraison.php`

And will allow the tracking endpoint:

`.../nextgen/api/trajet.php?id_livraison=...`

6. Build APK:
Build → Build Bundle(s) / APK(s) → Build APK(s)
