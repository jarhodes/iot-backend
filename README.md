# iotstate
Simple PHP backend for IoT project

This contains a simple interface for adding a new state and retrieving the latest state

## Usage
HTTP requests as follows:

GET getState.php?key={API key}
  - Returns a JSON object with the state name and UNIX timestamp

POST postState.php?key={API key}
  - Requires a simple body string (not JSON) with the variable "state" and a valid state name
  
## State names
The valid state names are:
up
down
stopped
