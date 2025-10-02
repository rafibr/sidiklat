import sharp from 'sharp';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

async function generatePWAIcons() {
	const buildDir = path.join(__dirname, 'public', 'build');

	// Create build directory if it doesn't exist
	if (!fs.existsSync(buildDir)) {
		fs.mkdirSync(buildDir, { recursive: true });
	}

	console.log('Generating PWA icons...');

	// Create a simple placeholder icon
	const placeholderSvg = `
        <svg width="192" height="192" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#3B82F6;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#8B5CF6;stop-opacity:1" />
                </linearGradient>
            </defs>
            <rect width="192" height="192" fill="url(#grad1)"/>
            <circle cx="96" cy="96" r="40" fill="white" opacity="0.9"/>
            <text x="96" y="110" font-family="Arial, sans-serif" font-size="48" font-weight="bold" fill="#3B82F6" text-anchor="middle">S</text>
        </svg>
    `;

	try {
		// Generate 192x192 icon
		await sharp(Buffer.from(placeholderSvg))
			.png()
			.toFile(path.join(buildDir, 'manifest-icon-192.png'));
		console.log('✓ Generated 192x192 icon');

		// Generate 512x512 icon
		await sharp(Buffer.from(placeholderSvg))
			.resize(512, 512)
			.png()
			.toFile(path.join(buildDir, 'manifest-icon-512.png'));
		console.log('✓ Generated 512x512 icon');

		// Generate 150x150 icon for Microsoft tiles
		await sharp(Buffer.from(placeholderSvg))
			.resize(150, 150)
			.png()
			.toFile(path.join(buildDir, 'manifest-icon-150.png'));
		console.log('✓ Generated 150x150 icon');

		console.log('\n✅ PWA icons generated successfully!');
		console.log('Icons saved to: /public/build/');

	} catch (error) {
		console.error('❌ Error generating PWA icons:', error);
	}
}

generatePWAIcons();
