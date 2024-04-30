# Changelog
- Written by Nicko :)
## [0.0.1] - 2024-01-17
### Added
- Project setup and initial commit.
- Created the basic structure of the weather app using PHP, including a header and footer.
- Implemented the main layout in HTML with a 3-day forecast placeholder.
- Established a styling baseline with CSS for a clean and modern design, using earthly and light colors.
- Set up a local development environment with WAMP server, ensuring all services are running.
- Integrated a PHP script to display the current date and time.
- Prepared `style.css` for general body styling, header, main content, footer, and responsive design considerations.
- Developed `script.js` for future dynamic client-side functionality.

## [0.0.2] - 2024-01-27
### Added
- Fixed the dates in 'Weather Container'
- Changed the time to EST instead of UTC (Maybe have it change to the time it is near you. I can determine this when they type in their zipcode)

## [0.0.3] - 2024-01-29
### Added
- Dynamic weather forecast retrieval based on user-provided zipcode via OpenWeatherMap API integration.
- Asynchronous form submission in index.php to update weather data without reloading the page.
- JavaScript event handling for form submission, API data fetching, and DOM updates in script.js.
- Clearing and updating of forecast information upon receiving new API data.
- Fixed: 
    - Date display to correctly start from the current day, accounting for EST timezone.
    - Forecast data showing the same values for min and max temperatures by accurately processing API data.
- Changed
    - Timezone handling from UTC to EST in index.php to align with the user's local time.
    - JavaScript logic to group API forecast data by local days and display daily high and low temperatures.
    - Styles in style.css to better accommodate the zipcode input form and improve the overall layout.
- Updated
    - The user interface to respond to invalid zipcode input with user-friendly alerts.
    - Debugging output in the console for better tracking of application state and errors.
- Possible Additions:
    - Use another API to determine users location and retrieve zipcode automatically (If user grants permission)
    - Add more detail, maybe even pictures for the forecast

## [0.0.4] - 2024-04-30
### Added
- Added server-side validation for ZIP codes to enhance security and ensure data integrity.
- Hid API key from client-side to secure sensitive information and comply with best practices.
- Improved error handling to provide clearer error messages and enhance user experience.
- Added to Github






