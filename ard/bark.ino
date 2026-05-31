#include <Servo.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>

Servo myServo;
LiquidCrystal_I2C lcd(0x27, 16, 2);

int gateIR = 2;
int lane1 = 7;
int lane2 = 8;
int lane3 = 9;

int lastState = HIGH;

int lastL1 = HIGH;
int lastL2 = HIGH;
int lastL3 = HIGH;

String lastData = "";
String lastLine1 = "";
String lastLine2 = "";

void setup() {
  Wire.begin();  // يبدأ كـ Master
  myServo.attach(3);
  pinMode(gateIR, INPUT);
  pinMode(lane1, INPUT);
  pinMode(lane2, INPUT);
  pinMode(lane3, INPUT);

  myServo.write(0);
  lcd.init();
  lcd.backlight();
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("  System Ready  ");
  delay(3000);
  lcd.clear();
}

void loop() {
  int currentState = digitalRead(gateIR);
  int l1 = digitalRead(lane1);
  int l2 = digitalRead(lane2);
  int l3 = digitalRead(lane3);

  // إرسال حالة الخطوط مجمعة (زي كودك القديم)
  String data = "L1:" + String(l1 == LOW ? "C" : "E") + ",";
  data += "L2:" + String(l2 == LOW ? "C" : "E") + ",";
  data += "L3:" + String(l3 == LOW ? "C" : "E");

  if (data != lastData) {
    Wire.beginTransmission(8);
    Wire.write(data.c_str());
    Wire.endTransmission();
    lastData = data;
  }

  // إرسال IN/OUT عند التغير في الحالة
  checkAndSendChange("L1", l1, lastL1);
  checkAndSendChange("L2", l2, lastL2);
  checkAndSendChange("L3", l3, lastL3);

  lastL1 = l1;
  lastL2 = l2;
  lastL3 = l3;

  // التحكم في البوابة
  if (l1 == LOW && l2 == LOW && l3 == LOW) {
    updateLCD(" FULL CAPACITY ", "   No Parking   ");
    for (int i = myServo.read(); i >= 0; i--) {
      myServo.write(i);
      delay(10);
    }
    lastState = HIGH;
    delay(500);
    return;
  }

  if (currentState != lastState) {
    if (currentState == LOW) {
      updateLCD(" Opening Gate ", " Please Wait... ");
      for (int i = myServo.read(); i <= 70; i++) {
        myServo.write(i);
        delay(10);
      }
      delay(2000);
    } else {
      delay(2000);
      for (int i = myServo.read(); i >= 0; i--) {
        myServo.write(i);
        delay(10);
      }
    }
    lastState = currentState;
  }

  String line1 = "L1:" + String(l1 == LOW ? "Car" : "Empty") +
                 " L2:" + String(l2 == LOW ? "Car" : "Empty");
  String line2 = "L3:" + String(l3 == LOW ? "Car" : "Empty");

  updateLCD(line1, line2);

  delay(200);
}

void checkAndSendChange(String lane, int current, int last) {
  if (current != last) {
    String status = (current == LOW) ? "IN" : "OUT";
    String message = lane + ":" + status;

    Wire.beginTransmission(8);
    Wire.write(message.c_str());
    Wire.endTransmission();

    Serial.println("Change Detected → Sent: " + message);
    delay(100);
  }
}

void updateLCD(String line1, String line2) {
  if (line1 != lastLine1) {
    lcd.setCursor(0, 0);
    lcd.print("                ");
    lcd.setCursor(0, 0);
    lcd.print(line1);
    lastLine1 = line1;
  }

  if (line2 != lastLine2) {
    lcd.setCursor(0, 1);
    lcd.print("                ");
    lcd.setCursor(0, 1);
    lcd.print(line2);
    lastLine2 = line2;
  }
}
