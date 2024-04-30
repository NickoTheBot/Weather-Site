<?php
require 'config.php';
$apiKey = defined('API_KEY') ? API_KEY : '';

require __DIR__ . '/vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

// Initialize logger
$logger = new Logger('weather_app_logger');
$logger->pushHandler(new RotatingFileHandler(__DIR__.'/app.log', 30, Logger::WARNING));

// Server-side validation and sanitation
$zipcode = filter_input(INPUT_POST, 'zipcode', FILTER_SANITIZE_NUMBER_INT);

// Verify the ZIP code 
if (!empty($zipcode) && preg_match('/^\d{5}$/', $zipcode)) {
    // API endpoint with sanitized ZIP code
    $apiUrl = 'http://api.openweathermap.org/data/2.5/forecast?zip=' . $zipcode . '&appid=' . $apiKey;

    // Initialize cURL session
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $apiUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

    $response = curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    if ($response === false) {
        $error = curl_error($curl);
        $logger->error('cURL Error: ' . $error);
        $error_message = "Sorry, we're having trouble retrieving the data. Please try again later.";
    } elseif ($http_status != 200) {
        $logger->error('API Request Error: HTTP Status ' . $http_status);
        $error_message = "Sorry, we're having trouble retrieving the data. Please try again later.";
    } else {
        $data = json_decode($response, true);
        
    }

    curl_close($curl);
} else {
    // Log invalid ZIP code warning
    $logger->warning('Invalid ZIP code received: ' . $zipcode);
    $error_message = "Invalid ZIP code entered.";
}



?>


<!doctype html>
<html lang="en">
<head>
    <title>My PHP Project</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Nicko">
    <meta name="description" content="Weather site">
    <meta charset="utf-8">
    <link href="style.css" rel="stylesheet" type="text/css"/>
    
   
</head>
<body>
<?php
    // Set the default timezone to use
    date_default_timezone_set('UTC');
    echo "<header><h1>Weather Forecast</h1></header>";
    
    // Get the current date and time
    $currentDate = date('Y-m-d'); 
    $currentTime = date('H:i');
    
    // Print the current date and time
    echo "<h2>It is: " . $currentDate . ' ' . $currentTime . "</h2>";
?>

    <main>
        <div class="zipcode-prompt">
            <form method="post" action="" id="zipcodeForm">
                <label for="zipcode">Enter your Zipcode:</label>
                <input type="text" id="zipcode" name="zipcode" pattern="\d{5}" title="Enter a 5-digit zipcode" required>
                <button type="submit">Get Weather</button>
            </form>
        </div>
        <div class="weather-container">
            <h2>3-Day Forecast</h2>
            <div class="forecast">
                <!--Forecast Display-->
                <?php 
                for ($i = 0; $i < 3; $i++) {
                    // Increment the date
                    $forecastDate = date('m-d-y', strtotime($currentDate . ' + ' . $i . ' days'));
                    
                    
                    echo "<div>
                            <h3>" . $forecastDate . "</h3>
                            <p><strong>Condition:</strong> [Condition for this day]</p>
                            <p><strong>High:</strong> [High for this day]°F</p>
                            <p><strong>Low:</strong> [Low for this day]°F</p>
                          </div>";
                }
                ?>
            </div>
        </div>
    </main>

    <footer>
        <p>© 2024 Weather App by Nicko. All rights reserved.</p>
    </footer>
    <script type="text/javascript">
        var apiKey = "<?php echo $apiKey;?>";
    var forecastContainer = document.querySelector('.forecast');
    onload = (forecastContainer.innerHTML = ''); // Clear existing forecast
    document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM fully loaded and parsed');

    // Add an event listener for the form submission
    document.getElementById('zipcodeForm').addEventListener('submit', function (e) {
        console.log('Form submitted');

        // Prevent the default form submission
        e.preventDefault();

        // Get the zipcode from the input
        var zipcode = document.getElementById('zipcode').value;
        console.log('Zipcode:', zipcode);

        // Check if the zipcode is valid
        if (zipcode.match(/^\d{5}$/)) {
            console.log('Zipcode is valid, making API call');

            // Make an API call to get the weather data
            fetch('http://api.openweathermap.org/data/2.5/forecast?zip=' + zipcode + ',us&appid=' + apiKey + '&units=imperial')
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(function (data) {
                    console.log('Data received from API:', data);
                    console.log(forecastContainer.childElementCount);

                    // Update the forecast container with new data
                    updateForecast(data);
                })
                .catch(function (error) {
                    console.error('There has been a problem with your fetch operation:', error);
                });
        } else {
            console.log('Invalid zipcode');
            alert('Please enter a valid 5-digit zipcode.');
        }
    });
});

function updateForecast(data) {
    forecastContainer.innerHTML = ''; // Clear existing forecast

    
    let forecastsByDay = groupForecastsByDay(data.list);
    const timezoneOffset = new Date().getTimezoneOffset();

    // Process each day's forecasts
    Object.keys(forecastsByDay).forEach((day, index) => {
        if (index < 3) { 
            let dayForecasts = forecastsByDay[day];
            let minTemp = Math.min(...dayForecasts.map(f => f.main.temp_min));
            let maxTemp = Math.max(...dayForecasts.map(f => f.main.temp_max));
            let condition = dayForecasts[0].weather[0].main;
            let date = new Date(dayForecasts[0].dt * 1000 - timezoneOffset * 60 * 1000);

            var forecastHtml = `
                <div>
                    <h3>${formatDate(date)}</h3>
                    <p><strong>Condition:</strong> ${condition}</p>
                    <p><strong>High:</strong> ${maxTemp}°F</p>
                    <p><strong>Low:</strong> ${minTemp}°F</p>
                </div>
            `;
            forecastContainer.innerHTML += forecastHtml;
        }
    });
}
function groupForecastsByDay(forecasts) {
    const timezoneOffset = new Date().getTimezoneOffset();
    return forecasts.reduce((groups, forecast) => {
        let date = new Date(forecast.dt * 1000 - timezoneOffset * 60 * 1000);
        let day = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
        if (!groups[day]) {
            groups[day] = [];
        }
        groups[day].push(forecast);
        return groups;
    }, {});
}

function formatDate(date) {
    return (date.getMonth() + 1) + '-' + date.getDate() + '-' + date.getFullYear().toString().substr(-2);
}
    </script>
    
</body>
</html>