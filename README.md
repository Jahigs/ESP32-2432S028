OVERVIEW:
Based on https://github.com/makeitworktech/ESP32-CYD-ESPHome

Slightly modified to serve a specific task: a copy count message creator.

HARDWARE USED:
ESP32-2432S028 (ILI9341 Screen) https://www.amazon.com/dp/B0DNM4SKSJ?ref=fed_asin_title (used for configFile1.yaml)
ESP32-2432S028 (ST7789V Screen) https://www.amazon.com/gp/product/B0DDPY97JC/ref=ox_sc_act_title_1?smid=A2JLTKYCWT3GQ2&th=1 (used for configFile2.yaml)
Zebra ZD621 https://www.barcodegiant.com/zebra/part-zd6a042-d01f00ez.htm?utm_medium=pla&utm_campaign=PLA_Topaz&gclsrc=aw.ds&&utm_source=google&utm_medium=cpc&utm_campaign=938782310&gad_source=1&gad_campaignid=938782310&gbraid=0AAAAAD_vazhiZe-d50tR-874VgmnO9Wka&gclid=Cj0KCQjwjL3HBhCgARIsAPUg7a6NnxQxvc9NlKyv-gOV3uZ8OUaDmE4PqDRb4glURN4oh1gLLACczUUaAu0WEALw_wcB
                                                        
You should be familiar with the following environments:

ESPHOME
Home Assistant
Node-RED



ESPHOME & Home Assistant:
add a new YAML file with one of the project configurations and adopt the device (change out parameters in the file before install)

Node-RED & Home Assistant:
After device is adopted, workflow can be created in node-RED, sequence:
MQTT NODE:
  Action: subscribe to single topic
  Topic: YOUR-DEVICE-NAME-HERE/print/copies
  QoS: 2
  Output: auto-detect

CHANGE NODE:
Name: YOUR-PRINTER-NAME
  Rules: set msg.printer to the value YOUR-PRINTER-NAME

FUNCTION NODE:
Name: function 1
On Message tab:
  msg.qty = 1
msg.qty = msg.payload
msg.url = 'path/to/your/server/.php?json={"printer":"' + msg.printer + '","qty":"' + msg.qty + '","key":"a8371d84f4b28a32ab4b7f8ef5033bec4869090e8fa5caab705a88db10a89c54"}'
return msg;

DELAY NODE:
  Action: Rate Limit - All messages
  Rate: 1 msg(s) per 5 seconds
  Queue intermediate messages

HTTP REQUEST NODE:
  Method: GET
  Payload: Ignore
