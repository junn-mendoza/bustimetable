## test

We have a client that we manage a bus timetable system for. We receive an unknown amount of XML files via FTP (FTP is out of the scope of this, attached are a zip of some example files). Can you review the XML data and design out the database that will allow for the most efficient storage of data (given we don't know how many routes we may receive) and then complete the following,

1. Create Laravel Models that relate to the new DB structure including relationships.

### Located App/Models
### database/migrations

### run - php artisan migrate


2. Build a console command that will import the XML (ideally from a folder of files) into the new table.

### Class use - App\Helper\Tools

### Clone the the repository

### Data is created in sqlite (databse/db.sqlite) - Please change the path in .env file

### FTP download variable directory is .env file - BUSTABLE_WORKING_DIR

### To run the scedule console file - php artisan schedule:run