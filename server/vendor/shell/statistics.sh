#/bin/bash
# @author: xiaokai
# @date: 2017-10-09
dname=`date +%Y%m%d`
day=`date +%Y-%m-%d`
day1=`date +%Y-%m-%d`
if [ ! -x "/www/talk/public/cron/$dname"]; then
 mkdir -p "/www/talk/public/cron/$dname"
echo "SELECT	u.user_id,u.alias,l.id as liveId,(SELECT	COUNT(id)	FROM	talk_live_focus f	WHERE	f.live_id = l.id AND f.robot = 2 AND FROM_UNIXTIME(UNIX_TIMESTAMP(f.create_time),'%Y-%m-%d') BETWEEN "$day" AND "$day1") AS count
FROM	talk_user u LEFT JOIN talk_live l ON l.user_id = u.user_id WHERE	u.user_level = 2 AND l.id <> '';" | mysql -h 123.103.74.8 -P 3308 -u talk_rw -pm7ZokXmoLAk8yQ13 -D talk -e > /www/talk/public/cron/"$dname"/test.xls;