#/usr/bin

su nginx
cd /mnt/yingmai
/usr/bin/php think AccountCheck >> runtime/cli/AccountCheck.log
/usr/bin/php think CoinCount >> runtime/cli/CoinCount.log
/usr/bin/php think TransCount >> runtime/cli/TransCount.log
/usr/bin/php think UserCount >> runtime/cli/UserCount.log