#include <Wire.h>
#include <WiFi.h>
#include <HTTPClient.h>

const char* ssid = "H";
const char* password = "Mohamed00112200##";
const char* serverURL = "http://192.168.1.4/test/car.php";

String receivedData = "";

void receiveEvent(int bytes) {
  receivedData = "";
  while (Wire.available()) {
    char c = Wire.read();
    receivedData += c;
  }
  Serial.println("Received: " + receivedData);
}

void setup() {
  Serial.begin(115200);
  Wire.begin(0x08);  // I2C address as slave
  Wire.onReceive(receiveEvent);

  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println(" Connected!");
}

void loop() {
  if (receivedData.length() > 0) {
    sendToServer(receivedData);
    receivedData = "";
  }
  delay(500); // أقل تأخير لتقليل الضغط
}

void sendToServer(String data) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverURL);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    String postData = "lane_data=" + data;

    int httpResponseCode = http.POST(postData);
    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println("Server Response: " + response);
    } else {
      Serial.println("Error sending to server");
    }
    http.end();
  } else {
    Serial.println("WiFi Not Connected");
  }
}
