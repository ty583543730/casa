#!/bin/bash

cd /mnt/yingmai
php think queue:restart
nohup php think queue:work --daemon  --queue CheckUpgradeJobQueue > runtime/cli/CheckUpgradeJobQueue.log 2>&1 &
nohup php think queue:work --daemon --queue RewardsJobQueue > runtime/cli/RewardsJobQueue.log 2>&1 &
