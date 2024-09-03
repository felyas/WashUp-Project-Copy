/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./build/**/*.{html,js,php}"],
  theme: {
    extend: {
      colors: {
        federal: 'var(--federal)',
        fedHover: 'var(--fedHover)',
        fedActive: 'var(--fedActive)',
        polynesian: 'var(--polynesian)',
        celestial: 'var(--celestial)',
        seasalt: 'var(--seasalt)',
        ashblack: 'var(--ashblack)',
        sunrise: 'var(--sunrise)',
      },
      fontFamily: {
        poppins: ['Poppins', 'sans-serif'],
      }
    },
  },
  plugins: [],
}

