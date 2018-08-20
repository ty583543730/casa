#/usr/bin

su nginx
cd /mnt/yingmai
/usr/bin/php think CoinmarketcapPrice >> runtime/cli/coinmarketcapPrice.log
/usr/bin/php think PurseAddress >> runtime/cli/purseAddress.log
/usr/bin/php think WalletCheck >> runtime/cli/walletCheck.log