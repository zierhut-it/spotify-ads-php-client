# Spotify Ads PHP Client

This is an unofficial PHP Client for the [Spotify Marketing API](https://developer.spotify.com/documentation/ads-api/).

[![Maintenance](https://shields.io/badge/Mainained%3F-archived-red?style=for-the-badge)](https://gitHub.com/zierhut-it/spotify-ads-php-client/graphs/commit-activity)

## API Coverage
- [ ] Ad Accounts
- [ ] Advertiser
- [ ] Campaigns
- [ ] Assets
- [x] Reports
- [ ] Targets
- [ ] Ad Sets
- [ ] Ads
- [ ] Estimate

## Installation

Use the package manager [composer](https://getcomposer.org/) to install.

```bash
composer require zierhut-it/spotify-ads-php-client
```

## Getting started

### Authenticate
```PHP
use Spotify\Api\Marketing\Client;

require_once __DIR__ . "/vendor/autoload.php";

$spotify = new Client(
    "<your-client-id>",
    "<your-client-secret>",
);

// This has to be opened in a browser to grant access
$url = $spotify->auth->getRedirectUrl();

// Save the result for later reuse
$refreshToken = $spotify->auth->getRefreshToken("<code received from the callback>");
```

### Keep logins
```PHP
// You may set the refresh token again next time, so no new login and callback is needed
$spotify->auth->setRefreshToken("<your refresh token>");
```

### Run a report
```PHP
$report = $spotify->newReport();

// You can set options using a simple string
$report->addAdAccountId("<optional-ad-account-id>");

// Or chain multiple of those
$report
    ->addDimension("CAMPAIGN")
    ->addDimension("AD_SET");

// Or don't use chaining
$report->addField("CLICKS");
$report->addField("IMPRESSIONS");

// Or pass multiple values
$report->addField("CTR", "SPEND");

// Or even arrays
$report->addField(["COMPLETION_RATE", "COMPLETES"]);

// When all parameters are set to your liking, make the actual request
$results = $report->run();

// You can use the returned $results or just iterate the report
foreach($report as $row) {
    print_r($row);
}
```

## Contributing

Pull requests and Issues are welcome. For major changes, please open an issue first
to discuss what you would like to change.
