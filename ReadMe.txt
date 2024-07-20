*** 1. REQUIERMENTS ***
   1. Apache and MySQL web servers.
   2. Composer dependency manager.
   3. Git software

*** 2. INSTALLATION ***
  - To properly install and run the app, you need to:

  *** Download the project ***
  - To download source code, you just need to git clone the project. Command `git clone https://github.com/CvijicZ/friendflow.git`
     - Just make sure that you run command above in the folder where your server will be looking for it (htdocs on xampp)
  - After you cloned the project, do not forget to run ** `composer install` ** to install dependencies.

  *** Setting up database and it's tables ***
    1. Start your DBMS (mysql server)
    2. Create database with the name you want (make sure to set that name in .env file)
    3. Create .env file and populate your credentials (you have .env.example, just copy it and rename it to .env)
    4. Open your terminal from project's root folder (friendflow) and type "php migrate.php", you should see Database table 'tableName' created for each table

  *** Setting up websockets server ***
    - If you are running on localhost:
       1. Open public/js/app.js, at the top of the script you will find constant WS_IP & WS_PORT, put localhost for the IP and chose your port (8080 by default)
       2. Now open your .env and set your WebSocket server (host and ip adress)
         - For localhost it would be localhost for host and 127.0.0.1 for ip, use the SAME port here as in the app.js (8080 by default)

    - If you are running on the host service or something:
       1. Open public/js/app.js, at the top of the script you will find constant WS_IP & WS_PORT, put your ws server IP and your port
       2. Open .env file and put your WS host and the IP of the server, make sure that port is the right one.

   - NOTICES:
      1. You need to make sure that port you chose here is open for inbound and outbound traffic on machine that is used for WS server
      2. You need to make sure that port is not used by other app or you could have port conflicts
      3. *IMPORTANT*, you need to make sure that WS server is up and running, to run the server you need to go to root of the project,
            open terminal and type 'php server.php', you will get return message "Websocket server started", and after that you can
             see in the console when new user connects to the WS or sends requests to the WS.

    *** Running the project ***
    1. Make sure that your servers (apache and mysql) are up and running 
    2. Open your browser and type in the url "localhost/friendflow" (this can variate based on your OS and server you are using)
    3. You are all set, create account and enjoy the platform :)