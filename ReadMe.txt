*** 1. INSTALLATION ***
  - To properly install and run the app, you need to:

  *** Downloading project ***
  - To download source code, you just need to git clone the project

  *** Setting up database and it's tables ***
    1. Start your DBMS (mysql server)
    2. Create database with the name you want (make sure to set that name in .env file)
    3. Create .env file and populate your credentials (you have .env.example, just copy it and rename it to .env)
    4. Open your terminal from project's root folder (friendflow) and type "php migrate.php", you should see Database table 'tableName' created for each table

    *** Running the project ***
    1. Make sure that your servers (apache and mysql) are up and running 
    2. Open your browser and type in the url "localhost/friendflow" (this can variate based on your OS and server you are using)
    3. You are all set, create account and enjoy the platform :)