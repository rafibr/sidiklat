/** @type {import('tailwindcss').Config} */
export default {
	content: [
		"./resources/**/*.blade.php",
		"./resources/**/*.js",
		"./resources/**/*.vue",
	],
	theme: {
		extend: {
			screens: {
				'xs': '475px',
			},
			animation: {
				'fade-in': 'fadeIn 0.5s ease-in-out',
				'slide-up': 'slideUp 0.3s ease-out',
				'bounce-soft': 'bounceSoft 0.6s ease-in-out',
			},
			keyframes: {
				fadeIn: {
					'0%': { opacity: '0' },
					'100%': { opacity: '1' },
				},
				slideUp: {
					'0%': { transform: 'translateY(20px)', opacity: '0' },
					'100%': { transform: 'translateY(0)', opacity: '1' },
				},
				bounceSoft: {
					'0%, 20%, 50%, 80%, 100%': { transform: 'translateY(0)' },
					'40%': { transform: 'translateY(-5px)' },
					'60%': { transform: 'translateY(-3px)' },
				}
			},
		},
	},
	plugins: [
		require('@tailwindcss/forms'),
	],
}
