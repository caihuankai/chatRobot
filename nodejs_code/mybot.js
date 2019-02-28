const { Wechaty } = require('wechaty')
var server = require('./curl')
const bot = new Wechaty()
    //获取微信的登录的二维码图片
bot.on('scan', (qrcode, status) => {
        if (!/201|200/.test(String(status))) {
            let loginUrl = qrcode.replace(/\/qrcode\//, '/l/')
            require('qrcode-terminal').generate(loginUrl, { small: true })
        }
        console.log(['https://api.qrserver.com/v1/create-qr-code/?data=', encodeURIComponent(qrcode), '&size=220x220&margin=20', ].join(''))
    })
    //登录回调
bot.on('login', user => console.log(`User ${user} logined`))
    //退出登录回调
bot.on('logout', user => console.log(`User ${user} logouted`))

//收到消息（发消息的时候也会调用）
bot.on('message', message => {
    console.log(`Message: ${message}`)
    disposeMessage(message);
    return;
})

bot.start()

/**
 * 
 * @param {msg} 要处理的消息
 */
function disposeMessage(msg) {
    const contact = msg.from() // 发送者
    const text = msg.text()

    const room = msg.room()

    if (room) { //群聊消息
        const topic = room.topic()
        console.log(`Room: ${topic} Contact: ${contact.name()} Text: ${text}`)
    } else {
        if (!msg.self() && /[0-9]{6}/.test(text)) {
            server.download(text, (res) => {
                if (res.data) {
                    let data = res.data[0]
                    console.log(`发送人: ${contact.name()} 发送消息: ${data.guNum}  ${data.diagnosis}  ${data.cnt}`)
                    resposeMsg = `股票代码: ${data.guNum} \n${data.diagnosis}\n  ${data.cnt}`
                    msg.say(resposeMsg)
                }
            })
        }
    }
}