see the features files. more instructions there
# testing log
vendor/bin/behat --config tests/behat/behat.yml --tags auth @logs
# testing auth
vendor/bin/behat --config tests/behat/behat.yml --tags auth @auth