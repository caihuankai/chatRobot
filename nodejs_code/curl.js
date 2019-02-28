var superagent = require('superagent');
var charset = require('superagent-charset');
charset(superagent);
const cheerio = require('cheerio');
const baseUrl = 'http://doctor.10jqka.com.cn/'

// Utility function that downloads a URL and invokes
// callback with the data.
function download(num, cb) {
    var num = num || '000001'
    superagent.get(baseUrl + num)
        .charset('gb2312')
        .end(function(err, sres) {
            var items = [];
            if (err) {

                cb({ code: 400, msg: err, sets: items });
                return;
            }
            var $ = cheerio.load(sres.text);
            // div.wrapper div.inner .fl.stockall .stockname a
            $('div.wrapper .box1 div.inner').each(function(idx, element) {
                var $element = $(element);
                var $guNumP = $element.find('.stockname a');
                var guNum = $guNumP.text();
                var $generalP = $element.find('.stocktotal');
                var general = $generalP.text();
                var $cntP = $element.find('.cnt');
                var cnt = $cntP.text().split('[')[0];
                items.push({
                    guNum: guNum,
                    diagnosis: general,
                    cnt: cnt
                });
            });

            cb({ code: 200, msg: "", data: items })
        })
}

exports.download = download;