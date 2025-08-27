# PWA Setup Guide - SIDIKLAT

## Progressive Web App Features

SIDIKLAT telah dikonfigurasi sebagai Progressive Web App (PWA) dengan fitur-fitur berikut:

### ✅ Implemented Features

1. **Web App Manifest** (`/public/manifest.json`)
   - App name, description, and icons
   - Theme colors and display mode
   - Screenshots for app store listings

2. **Service Worker** (`/public/sw.js`)
   - Offline caching for critical resources
   - Background sync capability
   - Push notification support (ready for implementation)

3. **Offline Page** (`/public/offline.html`)
   - User-friendly offline experience
   - Auto-retry connection detection
   - Feature list for offline capabilities

4. **Touch Optimizations** (`/resources/css/app.css`)
   - Touch-friendly button sizes (44px minimum)
   - Improved tap targets
   - Touch-specific animations and feedback

5. **Install Prompt** (`/resources/js/pwa.js`)
   - Automatic install prompt handling
   - Install button for manual installation
   - Update notifications

6. **Connection Status**
   - Real-time online/offline detection
   - Visual indicators for connection status
   - Graceful degradation for offline use

### 🔧 Setup Instructions

#### 1. Generate PWA Icons

Untuk manifest yang lengkap, Anda perlu membuat icons dengan berbagai ukuran:

```bash
# Install sharp for image processing (if not already installed)
npm install sharp --save-dev

# Create icons from a source image (192x192 recommended)
# Place your source icon as /public/icon-source.png
npx sharp /public/icon-source.png --output /public/build/ --name manifest-icon --sizes 192 512
```

Atau gunakan online tools seperti:
- [PWA Asset Generator](https://github.com/onderceylan/pwa-asset-generator)
- [Real Favicon Generator](https://realfavicongenerator.net/)

#### 2. HTTPS Requirement

PWA memerlukan HTTPS di production. Pastikan:
- SSL certificate terinstall
- Semua resources served over HTTPS
- Service worker hanya berjalan di secure contexts

#### 3. Testing PWA

Gunakan Chrome DevTools untuk testing:
1. Buka DevTools > Application > Manifest
2. Check Service Workers section
3. Test offline functionality di Network tab
4. Test install prompt di Console

#### 4. Build Assets

```bash
npm run build
```

### 📱 Mobile Features

#### Touch Optimizations
- Minimum 44px touch targets
- Improved button spacing
- Touch-specific hover states
- Optimized font sizes for mobile

#### Offline Capabilities
- Cached dashboard data
- Cached progress information
- Cached pelatihan lists
- Offline navigation

#### Install Experience
- Add to Home Screen prompt
- Standalone app mode
- Native app-like experience

### 🔄 Update Strategy

Service worker akan otomatis:
1. Download update di background
2. Show update notification
3. Apply update on next visit

### 📊 Browser Support

- Chrome 70+
- Firefox 68+
- Safari 12.1+ (iOS 12.2+)
- Edge 79+

### 🚀 Deployment Checklist

- [ ] Generate and place PWA icons
- [ ] Test offline functionality
- [ ] Verify HTTPS setup
- [ ] Test install prompt
- [ ] Test on mobile devices
- [ ] Update manifest with correct URLs

### 🐛 Troubleshooting

#### Service Worker Not Registering
- Check browser console for errors
- Ensure HTTPS in production
- Verify service worker file path

#### Icons Not Loading
- Check icon paths in manifest.json
- Ensure icons exist in /public/build/
- Verify correct sizes and formats

#### Install Prompt Not Showing
- App might already be installed
- Check for beforeinstallprompt event
- Verify PWA criteria are met

### 📞 Support

Untuk pertanyaan tentang PWA implementation, silakan cek:
- [PWA Documentation](https://developers.google.com/web/progressive-web-apps)
- [Service Worker API](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API)
- [Web App Manifest](https://developer.mozilla.org/en-US/docs/Web/Manifest)
