/*
* /$$      /$$           /$$              /$$$$$$                      /$$                   /$$    
*| $$  /$ | $$          | $$             /$$__  $$                    | $$                  | $$    
*| $$ /$$$| $$  /$$$$$$ | $$$$$$$       | $$  \__/  /$$$$$$   /$$$$$$$| $$   /$$  /$$$$$$  /$$$$$$  
*| $$/$$ $$ $$ /$$__  $$| $$__  $$      |  $$$$$$  /$$__  $$ /$$_____/| $$  /$$/ /$$__  $$|_  $$_/  
*| $$$$_  $$$$| $$$$$$$$| $$  \ $$       \____  $$| $$  \ $$| $$      | $$$$$$/ | $$$$$$$$  | $$    
*| $$$/ \  $$$| $$_____/| $$  | $$       /$$  \ $$| $$  | $$| $$      | $$_  $$ | $$_____/  | $$ /$$
*| $$/   \  $$|  $$$$$$$| $$$$$$$/      |  $$$$$$/|  $$$$$$/|  $$$$$$$| $$ \  $$|  $$$$$$$  |  $$$$/
*|__/     \__/ \_______/|_______/        \______/  \______/  \_______/|__/  \__/ \_______/   \___/  
*
* @author: $rachow
* @copyright: XM App 2023
* @file: websocket.js
*
* Full Duplex connection to websocket server
*   secure = 'wss://' vs nonsecure = 'ws://'
*
*   todo: establish socket server exists and connect to
*         pull the OHLC(V) data at frequency
*
*         if disconnected then attempt to reconnect
*         send error to stability monitoring service.
*
*/

const ws_port = '6602';
const ws_url = 'ws://localhost:' + ws_port;



