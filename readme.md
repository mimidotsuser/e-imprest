Imprest management involves:
- authorizing of requests of small funds of cash for small transactions which are not worth granting a cheque
- Accounting for use and proper utilization of the petty cash as per laid institution policies.

## Installation

#### Prerequisites

- Web server with PHP 7+ support.
- MySQl Server 5.6+ support.
- [Composer](https://getcomposer.org/) installed at your environment.

#### Setup
- Clone or download the project as a zip into your web server <br/>
- Run `composer install` to install all the project dependencies.
- Import the mysql database provided [here](REEBACK%20DATABASE%20EXPORT.sql). <br/>
- Rename `.env.example` file to `.env` . Do not forget to provide your valid configuration. More documentation on this can be found [here](https://github.com/vlucas/phpdotenv)

#### To setup a local mirror of the website:
        $ git clone https://github.com/mimidotsuser/e-imprest.git
        $ cd e-imprest
        $ composer install
        $ php -S localhost:80
        
If successful, use admin account (username & password=34556) to view other staff active accounts.<br/>
For staff account username and password, use the staff ID <br/>
To enable two-way authentication change the app environment value to live i.e (APP_ENV=live) <br/>

              
