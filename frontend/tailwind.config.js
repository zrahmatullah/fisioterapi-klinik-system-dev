/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          dark: '#0B3948',      // Deep Petrol Teal (Sidebar)
          hover: '#135063',     // Sidebar hover
          active: '#15576B',    // Sidebar active item
          accent: '#2DD4BF',    // Active border accent
          teal: '#0F766E',      // Primary CTA / active tab
          tealHover: '#115E59', // Primary CTA hover
        },
        surface: {
          canvas: '#F3F6F8',    // Main workspace background
          banner: '#E6F2F5',    // Context header background
          bannerBorder: '#CBE3E9',
          metric: '#F0F6F8',    // Metric card / vital signs
          metricBorder: '#DCE8EC',
        },
        clinical: {
          slateDark: '#0F172A',
          slateBody: '#334155',
          slateMuted: '#64748B',
          danger: '#DC2626',
          warning: '#D97706',
          successBg: '#DCFCE7',
          successText: '#15803D',
          successDot: '#22C55E',
          border: '#E2E8F0',
        }
      },
      fontFamily: {
        sans: ['Inter', 'Plus Jakarta Sans', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        subtle: '0 1px 3px rgba(15, 23, 42, 0.04)',
        xs: '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
        '2xs': '0 1px 1px 0 rgba(0, 0, 0, 0.03)',
      }
    },
  },
  plugins: [],
}
