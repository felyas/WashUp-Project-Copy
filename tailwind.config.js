/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./build/**/*.{html,js,php}"],
  theme: {
    extend: {
      colors: {
        federal: 'var(--federal)',
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

