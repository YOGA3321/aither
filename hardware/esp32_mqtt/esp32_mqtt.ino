#include <WiFi.h>
#include <PubSubClient.h>

// WiFi Configuration
const char* ssid = "WIFI_SSID";
const char* password = "WIFI_PASSWORD";

// MQTT Configuration
const char* mqtt_server = "broker.hivemq.com";
const int mqtt_port = 1883;

// Device Configuration (Hardcoded)
const String API_KEY = "DEVICE_API_KEY_12345";
const String SECRET_KEY = "DEVICE_SECRET_KEY_ABCDE";
String topic = "iot/device/" + API_KEY + "/data";

WiFiClient espClient;
PubSubClient client(espClient);

unsigned long lastMsg = 0;

void setup() {
  Serial.begin(115200);
  
  // Connect to WiFi
  setup_wifi();
  
  client.setServer(mqtt_server, mqtt_port);
}

void setup_wifi() {
  delay(10);
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(ssid);

  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("");
  Serial.println("WiFi connected");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());
}

void reconnect() {
  // Loop until we're reconnected
  while (!client.connected()) {
    Serial.print("Attempting MQTT connection...");
    // Create a random client ID
    String clientId = "ESP32Client-";
    clientId += String(random(0, 1000));
    
    // Attempt to connect
    if (client.connect(clientId.c_str())) {
      Serial.println("connected");
    } else {
      Serial.print("failed, rc=");
      Serial.print(client.state());
      Serial.println(" try again in 5 seconds");
      delay(5000);
    }
  }
}

void loop() {
  if (!client.connected()) {
    reconnect();
  }
  client.loop();

  unsigned long now = millis();
  if (now - lastMsg > 2000) { // Send data every 2 seconds
    lastMsg = now;
    
    // Simulate Sensor Data
    int co2 = random(400, 1000);   // ppm
    int o2 = random(18, 22);       // %
    int pm25 = random(10, 50);     // ug/m3
    
    // Create JSON Payload
    String payload = "{";
    payload += "\"co2\":" + String(co2) + ",";
    payload += "\"o2\":" + String(o2) + ",";
    payload += "\"pm25\":" + String(pm25);
    payload += "}";
    
    // Publish to topic
    Serial.print("Publishing message: ");
    Serial.println(payload);
    client.publish(topic.c_str(), payload.c_str());
  }
}
