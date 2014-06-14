char srvIP[] = "127.0.0.1";
char srvHost[] = "host.domain.tld";
int srvPort = 80;
char srvPath[] = "/?l=uptime9";

void setup() {
    delay(5000);
    
    pinMode(D7, OUTPUT);
}

void loop() {
    httpGetRequest(srvIP, srvHost, srvPort, srvPath);
    Spark.sleep(SLEEP_MODE_DEEP, 1800);
}

void httpGetRequest(char* ip, char* hostname, int port, char* url) {
    delay(5000);
    
    digitalWrite(D7, HIGH);
    
    char line[255];
    
    TCPClient client;
    if(client.connect(ip, port)) {
        strcpy(line, "GET ");
        strcat(line, url);
        strcat(line, " HTTP/1.1");
        client.println(line);
        delay(100);
    
        strcpy(line, "Host: ");
        strcat(line, hostname);
        client.println(line);
        delay(100);
        
        strcpy(line, "Content-Length: 0");
        client.println(line);
        delay(100);
        
        client.println();
        delay(100);
        
        client.flush();
        delay(100);
        
        client.stop();
        delay(250);
    }
    
    digitalWrite(D7, LOW);
}
