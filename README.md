# Sigfox Migrate site

Drupal site setup to import and present loaction data fetched from Sigfox API.

## Content

* Drupal site is using github.com/drupal-composer/drupal-project
* `web/modules/custom/sigfox_storage_measurement` - Everything that was done using UI like content types for saving data and views for display.
* `web/modules/custom/sigfox_migrate` - Costom code implementing Migrate API to get data in from Sigfox.

## Get the site up and running

* Get the code: `git clone git@github.com:radimklaska/sigfox.git?`
* `cd sigfox`
* `composer install`
* `cp ./web/sites/default/default.settings.local.php ./web/sites/default/settings.local.php`
* Setup DB connection in `./web/sites/default/settings.local.php`
* Install the site using standard installation profile: `drush si -y`
* Install the module: `drush en sigfox_migrate -y`
* Login to the site: `drush user-login`
* Set the URL for the migration source:
  * with drush: `drush cset -y migrate_plus.migration.sigfox_location source.urls "https://USERNAME:PASSWORD@backend.sigfox.com/api/devices/DEVICEID/messages"`
  * or using UI: `/admin/config/sigfox_migrate/migrationconfig`
* Check migration status: `drush migrate-status`
* Run the migration: `drush migrate-import sigfox_location`
* Your site should now be working and populated with data. 

## Sigfox info

In general we need to fetch json object from remote URL. Parse it and save the result to node entity.

### API endpoint data

Here is and example. Note there is about 100 items with same structure in `data`:

```
{
  "data": [
    {
      "device": "4D2BA5",
      "time": 1515885642,
      "data": "af0b4b4255fc634100000100",
      "snr": "52.72",
      "linkQuality": "GOOD",
      "seqNumber": 2347,
      "rinfos": [
        {
          "tap": "2731",
          "delay": 1.5490000247955322,
          "lat": "51.0",
          "lng": "14.0"
        },
        {
          "tap": "272E",
          "delay": 0.652999997138977,
          "lat": "51.0",
          "lng": "14.0"
        },
        {
          "tap": "7A8C",
          "delay": 1.7070000171661377,
          "lat": "51.0",
          "lng": "14.0"
        },
        {
          "tap": "6D76",
          "delay": 0.6420000195503235,
          "lat": "51.0",
          "lng": "15.0"
        }
      ],
      "nbFrames": 3
    }
  ],
  "paging": {
    "next": "https://backend.sigfox.com/api/devices/4D2BA5/messages?limit=100&before=1515798520"
  }
}
```

### Message structure:

We are mainly interested in GPS coodinates. These are encoded in `data` string.

```
	a. (8B) GPS coordinates
	[0] latitude (-90 to 90)
	[1]
	[2]
	[3]
	[4] latitude (-90 to 90)
	[5]
	[6]
	[7]
	b. (2B) GPS distance since last measurement
	[8] distance in metres
	[9]
	c. (2B) GPS fail counter since last successfull fix
	[10] GPS fail counter
	[11]
```

`data` is Python's f-strings hexadecimally encoded.

Provided example:
```
<?php
$binarydata = "\xba\x0b\x4b\x42\x13\xfe\x63\x41";
$array = unpack("flat/flong", $binarydata);
print_r($array);
```

Decoding data in php:
```
<?php
$data = "af0b4b4255fc634100000100";
$array = unpack("flat/flong", hex2bin($data));
print_r($array);
```
