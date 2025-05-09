From the presentation:

During the presentation the manual altering of the user's budget and amount spent values wasn't working. Some other bugs were happening in the infinity free site that weren't happening on the localhost. Not sure why this is but if some aspects of the site are not functioning as intended it might be helpful to run XAAMP and, through the instructions below, attempt running the files on the localhost to double-check they function as intended.

Additionally, during the presentation we did not show the mobile side of the app which was run and tested through Android Studio (instructions below). For the final project we did not update functionality for the mobile function. As some of our SQL calls/tables were changed, this caused problems with using the version of the mobile app from homework 3. If you would still like to see the mobile app functionality, please refer to the SQL calls in the ReadMe up to issue "add comments #177" or the date April 11th. A few bug fixes were present in this version of the app that were later fixed in issues: fix mobile edit issues #181 and Bug Fix #183 (using the old SQL calls present in the ReadMe as far as April 11th). 

Created by Tara Pandey and Dalton Soper. 

This is a website created for an assignment for Comp 333, a Software Engineering course at Wesleyan University. 

Our app is designed to be an online budgeting system, where students can track the meal points they have spent and how that fits into their monthly budget. 

When you log into the app, you are taken to our budgeting demo. From there you can navigate around the app with the navbar. Certain pages in the navbar are only available if you are on a related page (such as the reviews, edit reviews, write reviews, comments and ratings page). We created a social component for the app with our ratings page which allows the user to see public data as well as their own. Our CRUD functionality is mostly through our reviews page, the user can write/edit/delete their own reviews and interact with reviews through comments. 

The work for this project was split 50/50

<img width="1085" alt="image" src="https://github.com/user-attachments/assets/192a395f-5754-4314-9a64-40ea8d5566d1" />
<img width="908" alt="image" src="https://github.com/user-attachments/assets/999d6370-21d7-4070-9734-371e55677c89" />

<img width="851" alt="Screenshot 2025-04-11 at 1 18 17 AM" src="https://github.com/user-attachments/assets/de69c331-fd0f-40c7-852d-418e03bf5042" />
<img width="733" alt="Screenshot 2025-04-11 at 1 18 54 AM" src="https://github.com/user-attachments/assets/2becc7df-1d08-4248-947b-0bd3634cc9a9" />

<img width="1439" alt="Screenshot 2025-03-07 at 6 45 21 PM" src="https://github.com/user-attachments/assets/46525793-7d5a-4b18-a57c-982cd398ba82" />

<img width="1470" alt="Screenshot 2025-03-07 at 11 37 08 PM" src="https://github.com/user-attachments/assets/5879ae59-fd49-43c4-8505-22db066df650" />

We used AI for general debugging and help with formatting/styling as well as the CORS functionality. AI was also used with incorporating JavaScript through the https://www.gstatic.com/charts/loader.js source.

**Setup**
Backend:
Make sure Apache and MySQL servers are running in XAMPP
In phpmyadmin (localhost) make sure to run this SQL code:

CREATE TABLE users (
    username VARCHAR(255) PRIMARY KEY,
    password TEXT,
    budget DECIMAL(10, 2),
    spent DECIMAL(10, 2),
    remainder DECIMAL(10, 2)
);

CREATE TABLE reviews (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    username TEXT,
    location TEXT,
    meal TEXT,
    rating INTEGER,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    review_text TEXT,
    comment_text TEXT
);

CREATE TABLE Menu (
    location VARCHAR(50),
    category VARCHAR(50),
    item VARCHAR(255),
    price DECIMAL(5,2)
);

INSERT INTO Menu (location, category, item, price) VALUES
('WesWings', 'Wings', 'Traditional Bone-In (8 pieces)', 12.95),
('WesWings', 'Wings', 'Traditional Bone-In (24 pieces)', 32.95),
('WesWings', 'Wings', 'Traditional Bone-In (36 pieces)', 43.95),
('WesWings', 'Wings', 'Boneless Breaded Tenders (5 pieces)', 12.95),
('WesWings', 'Wings', 'Boneless Breaded Tenders (10 pieces)', 20.95),
('WesWings', 'Wings', 'Vegetarian Breaded Seitan (7-10 pieces)', 12.95),
('WesWings', 'Wings', 'Vegetarian Breaded Seitan (16-20 pieces)', 18.95),
('WesWings', 'Specialty Sandwiches', 'CHICKEN PARMESAN', 12.95),
('WesWings', 'Specialty Sandwiches', 'KICKIN'' CHICKEN CLUB', 13.95),
('WesWings', 'Specialty Sandwiches', 'BLT', 12.95),
('WesWings', 'Specialty Sandwiches', 'TURKEY CLUB', 13.95),
('WesWings', 'Specialty Sandwiches', 'GARDEN BAGEL MELT', 12.95),
('WesWings', 'Specialty Sandwiches', 'Custom Sandwich', 12.95),
('WesWings', 'Sandwich Extras', 'BACON', 2.95),
('WesWings', 'Sandwich Extras', 'AVOCADO', 2.95),
('WesWings', 'Sandwich Extras', 'GOAT CHEESE', 2.95),
('WesWings', 'Grill', 'GRILLED CHICKEN SANDWICH', 10.95),
('WesWings', 'Grill', 'COUNTRY CLUB CHICKEN', 12.95),
('WesWings', 'Grill', 'GRILLED CHEESE SANDWICH', 4.95),
('WesWings', 'Grill', 'GRILLED CHEESE SANDWICH WITH TOMATO', 5.95),
('WesWings', 'Grill', 'GRILLED CHEESE IN A WRAP OR ON A GRINDER', 6.95),
('WesWings', 'Grill', 'BURGER', 7.95),
('WesWings', 'Grill', 'CHEESE BURGER', 8.95),
('WesWings', 'Grill', 'DOUBLE CHEESE BURGER', 12.95),
('WesWings', 'Grill', 'BBQ BACON CHEDDAR BURGER', 11.95),
('WesWings', 'Grill', 'VEGGIE BURGER', 8.95),
('WesWings', 'Grill', 'BLACK BEAN BURGER', 10.95),
('WesWings', 'Grill Extras', 'BACON', 2.95),
('WesWings', 'Grill Extras', 'AVOCADO', 2.95),
('WesWings', 'Grill Extras', 'FRIED EGG', 1.50),
('WesWings', 'Salads', 'GARDEN SALAD', 6.95),
('WesWings', 'Salads', 'CLASSIC CAESAR', 6.95),
('WesWings', 'Salad', 'Salad with protein', 12.95),
('WesWings', 'Salad Extras', 'BACON', 2.95),
('WesWings', 'Salad Extras', 'AVOCADO', 2.95),
('WesWings', 'Salad Extras', 'CRUMBLED GOAT CHEESE', 2.95),
('WesWings', 'Toast', 'AVOCADO TOAST', 9.95),
('WesWings', 'Toast Extras', 'BACON', 2.95),
('WesWings', 'Toast Extras', 'GOAT CHEESE', 2.95),
('WesWings', 'Toast Extras', '2 FRIED EGGS', 3.00),
('WesWings', 'Sides and Appetizers', 'MEDITERRANEAN SAMPLER', 9.95),
('WesWings', 'Sides and Appetizers', 'AMERICAN SAMPLER', 9.95),
('WesWings', 'Sides and Appetizers', 'FRIED MOZZARELLA STICKS (SMALL)', 7.25),
('WesWings', 'Sides and Appetizers', 'FRIED MOZZARELLA STICKS (LARGE)', 10.95),
('WesWings', 'Sides and Appetizers', 'CHICKEN TENDERS (SMALL)', 8.25),
('WesWings', 'Sides and Appetizers', 'CHICKEN TENDERS (LARGE)', 11.25),
('WesWings', 'Sides and Appetizers', 'FRENCH FRIES', 3.75),
('WesWings', 'Sides and Appetizers', 'SPICY FRIES', 3.75),
('WesWings', 'Sides and Appetizers', 'SOUP (8oz CUP)', 4.25),
('WesWings', 'Sides and Appetizers', 'SOUP (12oz BOWL)', 5.25),
('WesWings', 'Sides and Appetizers', 'SIDE GARDEN SALAD', 4.95),
('WesWings', 'Sides and Appetizers', 'CELERY & BLEU CHEESE', 3.25),
('RBC', 'Breakfast', 'EGG & CHEESE SANDWICH', 4.99),
('RBC', 'Breakfast', 'VEGGIE BREAKFAST WRAP', 6.99),
('RBC', 'Breakfast', 'CARDINAL BREAKFAST', 11.99),
('RBC', 'Build your own', 'BYO SALAD', 26.99),
('RBC', 'Build your own', 'BYO SANDWICH', 10.99),
('RBC', 'Build your own', 'BYO GRILLED CHEESE', 5.99),
('RBC', 'Build your own', 'BYO SMALL SMOOTHIE', 6.50),
('RBC', 'Build your own', 'BYO LARGE SMOOTHIE', 7.99),
('RBC', 'Specialty sandwiches', 'TURKEY PANINO', 11.99),
('RBC', 'Specialty sandwiches', 'TOMATO PANINO', 10.99),
('RBC', 'Specialty sandwiches', 'ITALIAN PANINO', 11.99),
('RBC', 'Specialty sandwiches', 'THANKSGIVING TURKEY', 11.99),
('Pi Cafe', 'Coffee', 'Drip Coffee', 2.95),
('Pi Cafe', 'Coffee', 'Iced Coffee', 3.45),
('Pi Cafe', 'Coffee', 'Latte', 4.55),
('Pi Cafe', 'Coffee', 'Espresso', 2.85),
('Pi Cafe', 'Drinks', 'Chai', 5.00),
('Pi Cafe', 'Drinks', 'Hot Chocolate', 4.15),
('Pi Cafe', 'Drinks', 'Hot Tea', 2.80),
('Pi Cafe', 'Drinks', 'Apple Cider', 2.75)
;

CREATE TABLE purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(255),
    created_at DATETIME,
    item_name VARCHAR(255) NOT NULL,
    item_price DECIMAL(10, 2) NOT NULL,
    location VARCHAR(255)
);

Link to the website through: https://wesspend.free.nf/

To load our website locally, first download all files and put them in your htdocs folder.
Then, setup an sql database named "login". Then in that database, under the SQL tag, copy and past the SQL code at the bottom of this page.
After the SQL database has been setup, navigate to localhost in a browser, taking you to the login page.
From the login page, you can create a username and password to log in and will then reach the landing page. There is a navbar on the top, you can click reviews to navigate to the reviews features (i.e. edit, delete, write).

(The start page leads to a demo of our budgeting app)

The mobile version does not have the latest updates made for the project

Before testing the Mobile version, make sure node.js is installed.

Make sure that you run all of these commands in your terminal to get the app working with Expo (cd into the budget file):

npm install -g expo-cli
cd budget
npm install @react-navigation/native @react-navigation/bottom-tabs react-native-webview @expo/vector-iconsnpx expo install react-native-screens react-native-safe-area-context
npx expo install react-native-screens react-native-safe-area-context

For the Mobile react version, run the code with an emulator in Android Studio. We used expo go, running the code npx expo start and navigating through the app on a real mobile device using the navbar at the bottom of the app. The mobile version also uses the localhost through XAAMP to call on our SQL database and tables. 

We hard-coded our IP address in lines: 55, 89, 146, 210, 447, 552, 648, 654 of the App.js file

Rest API:

The Rest API of our app functions through the Controller/Api, inc, and Model folders as well as the index2.php. The UpdateModel.php file in the Model folder updates the tables we call from our database through the CRUD and login/sign in functionalities. In the Model folder, the Database.php handles all connections with the Database and creates methods for executing SQL queries. In the Controller/Api folder, the BaseController.php deals with error, request, and response handling as well as session management. The UserController.php extends BaseController.php to implement specific endpoints and uses UserModel.php to interact with the database. Within the inc folder, config.php contains the configuration settings for the database and bootstrap.php is for the intialization and setup. Then through the index2.php, the main entry point, we process endpoint calls (i.e. /index2.php/user/list) and then matches them up with certain functions/commands on how the computer should handle these calls accordingly. We have several endpoints such as /user/login and /user/addreview that the App.js uses to call on the REST API.

Running the unit tests:

First make sure that homebrew, phpUnit, composer, and php are installed
Inside the htdocs folder, create a folder called "test-project" and run "composer init" in this folder following the testing setup (skipping most lines, for define dev dependencies, write phpunit/phpunit). Then, inside the test-project folder, make a folder called "tests". Place the 4 testing php files (testGet_UserList.php, testPost_CreateUser.php, testPost_FailedLogin.php, testPost_LoginUser.php) inside the tests folder. You can run the tests with php vendor/bin/phpunit tests/"name of test php file"
No repo changes were made from HW3.

Using Gen AI tools can make testing easy, especially given existing code examples. To create the POST request testing files you can copy and paste the GET testing files and ask an AI tool (I used ChatGPT) to create a POST request testing file similar to the GET test for creating a user with username and password fields. The result from ChatGPT is usually very helpful, only needing to change some specific things like links to files, the file name, and deleting a userid field that ChatGPT always puts in conjunction with a username field that we do not use. Once the first POST request testing file is craeted, it is very easy to get the other ones, as it is just changing around the POST request contents and possibly changing the path the request takes.

