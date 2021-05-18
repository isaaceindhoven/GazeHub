# End-to-end tests

Since the application is fragmented into 3 separate libraries it can be a bit daunting to check if everything works after a change. This can easily be solved with the E2E test library. All of the 3 libraries are tested in real time using their respected APIs.

### Requirements

Make sure you have the following tools installed:

| Name | Check Version
| --- | --- |
| NodeJS | `npm -v` |
| Composer | `composer -V` |
| Docker | `docker -v` |
| Docker Compose | `docker-compose -v` |


### Installation

```bash
npm install
```

### Running the tests

You can run the tests using the `npm run test` command. This will run the E2E with PHP 7.3. You can run `bash e2etest.sh` to run the E2E test with all of the supported versions of PHP (at moment those are 7.3, 7.4 and 8.0).
