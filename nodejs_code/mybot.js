const { Wechaty } = require('wechaty');
const bot = new Wechaty();
//获取微信的登录的二维码图片
bot.on('scan',    (qrcode, status) => console.log(['https://api.qrserver.com/v1/create-qr-code/?data=',encodeURIComponent(qrcode),'&size=220x220&margin=20',].join('')));
//登录回调
bot.on('login',   user => {
        console.log(`User ${user} logined`);
        console.log('登录获取好友列表');
    }
);
//退出登录回调
bot.on('logout',   user => console.log(`User ${user} logouted`));

//收到消息（发消息的时候也会调用）
bot.on('message', async m => {
    const contact = m.from();
    const text = m.text();
    const room = m.room();
    console.log(`收到消息 Contact: ${contact.name()} Text: ${text}`);
    console.log('消息类型：' + m.type());
    if (room) {//群聊消息
        const topic = room.topic();
        console.log(`群消息 Room: ${topic} Contact: ${contact.name()} Text: ${text}`);
    }
    //不是自己发送的消息、且消息类型为文字、则复读
    if(m.self() === false && m.type() == 7){
        // m.say('复读机：' + text);
        var http = require('http');  
        var qs = require('querystring');      
        var data = {  
            content:text,  
        };//需要提交的数据       
        var content = qs.stringify(data);  
        var options = {  
            hostname: 'test.talk.99cj.com.cn',  
            port:80,  
            path: '/Logintest/getbdbk?' + content,  
            method: 'GET'  
        };  
        var req = http.request(options, function (res) {
            res.setEncoding('utf8');  
            res.on('data', function (chunk) {  
                console.log('接口返回数据: ' + chunk);    
                m.say(chunk);
            });  
        });
        req.end();
    }
    
    if (/^dong$/i.test(text)) {
        await m.say('dingdingding');
    }
});

bot.start();
//百度百科
function baidubaike(text){
    var http = require('http');  
    var qs = require('querystring');      
    var data = {  
        content:text,  
    };//需要提交的数据       
    var content = qs.stringify(data);  
    var options = {  
        hostname: 'test.talk.99cj.com.cn',  
        port:80,  
        path: '/Logintest/getbdbk?' + content,  
        method: 'GET'  
    };  
    var req = http.request(options, function (res) {
        res.setEncoding('utf8');  
        res.on('data', function (chunk) {  
            console.log('接口返回数据: ' + chunk);    
            return  123;
        });  
    });
      
    req.end();
}
//消息类型
//图片类型：6
//文字类型：7
//视频类型：13
//红包：收到红包，请在手机上查看
//转账：[Received a micro-message transfer message, please view on the phone]
//小程序：[收到一条网页版微信暂不支持的消息类型，请在手机上查看]
//公众号类型：1