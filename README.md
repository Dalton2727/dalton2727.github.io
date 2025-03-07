Created by Tara Pandey and Dalton Soper. 

This is a website created for an assignment for Comp 333, a Software Engineering course at Wesleyan University. 
This website is incomplete and does not have the functionality that is said on the landing page. 
This website is comprised of two HTML files, one CSS file, and multiple images that are used in the html files. 

To run the code, all that is necessary is to download all files and open the start.html (or about.html, but start.html is the intended first page) which then has links via a navbar to the other html page. 
You can also access the website at https://dalton2727.github.io/start.html

The work for this project was split 50/50


SQL inputs:
first set up a database named "login". Then input the following command under SQL:
CREATE TABLE users (username VARCHAR(255) PRIMARY KEY, password TEXT);

CREATE TABLE reviews
    (id INTEGER PRIMARY KEY AUTO_INCREMENT,
    username TEXT,
    location TEXT,
    meal TEXT,
    rating INTEGER);

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
('RBC', 'Breakfast', 'TOASTED BAGEL WITH CREAM CHEESE', 2.99);
