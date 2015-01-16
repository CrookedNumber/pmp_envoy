BACKGROUND
==========
- This is a simple read-only tool to examine content in the Public Media Platform (PMP).
- You can toggle between production and sandbox, filter your queries, or jump to a particular PMP doc (provided you know the guid).
- DO NOT USE on a publicly available site (as it exposes permissioned content). In fact, I'd only recommend using it on a local server. It is some quick and dirty PHP, let me tell you.
- More about the PMP: http://publicmediaplatform.org/
- More about getting PMP credentials: https://support.pmp.io/docs#getting-started-user-registration

INSTALLATION
============
- This assumes you already have an *AMP environment running on your local box.
- Download the code

```
git clone this repo
```

- Get the PHP SDK and krumo

```
cd deputy
git clone git@github.com:publicmediaplatform/phpsdk.git
git clone git@github.com:oodle/krumo.git
```

- Create a credentials file

```
cp credentials.sample.php credentials.php
```

- Edit credentials.php, replacing `<redacted>` with your credentials
- Edit your hosts file and apache config files accordingly, so you can pull up deputy in your browser
- Browse the PMP!


