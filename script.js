var imgHelper = (function () {
    var MAX_WIDTH = 800;
    var MAX_HEIGHT = 600;

    var canvas = document.createElement('canvas');
    var context = canvas.getContext('2d');

    function adjustSize(sw, sh, dw, dh) {
        var x, y, w, h, scale;
        var dRatio = dh / dw;
        var sRatio = sh / sw;

        if (sw <= dw && sh <= dh) {
            scale = 1;
            w = sw;
            h = sh;
            x = parseInt((dw - sw) / 2);
            y = parseInt((dh - sh) / 2);
        }
        else {
            if (sRatio >= dRatio) {
                scale = dh / sh;
                y = 0;
                h = dh;
                w = sw * scale;
                x = parseInt((dw - w) / 2);
            }
            else {
                scale = dw / sw;
                x = 0;
                w = dw;
                h = sh * scale;
                y = parseInt((dh - h) / 2);
            }
        }

        return {
            scale: scale,
            w: w,
            h: h,
            x: x,
            y: y
        };
    }

    function minify(img, callback) {
        var nw = img.naturalWidth;
        var nh = img.naturalHeight;

        var ref = adjustSize(nw, nh, MAX_WIDTH, MAX_HEIGHT);
        var dataURL, blob;

        canvas.width = ref.w;
        canvas.height = ref.h;

        context.drawImage(img, 0, 0, ref.w, ref.h);

        dataURL = canvas.toDataURL();
        blob = dataURLtoBlob(dataURL);
        callback && callback(blob);

        context.clearRect(0, 0, ref.w, ref.h);
    }

    return {
        minify: minify
    };
})();




var views = {
    add: $('#view-add'),
    list: $('#view-list'),
    tags: $('#view-tags'),
    loading: $('#view-loading')
};

var currentView = views.loading;

function view(key) {
    currentView && currentView.hide();
    currentView = views[key];
    currentView.show();
}




var listView = {
    title: views.list.find('#list-title'),
    notes: views.list.find('#note-list'),
    pages: views.list.find('#page-list'),
    btnAdd: views.list.find('#btn-add'),
    btnBack: views.list.find('#btn-back'),
    list: views.list.find('#note-list'),
    tag: views.list.find('#tag'),
    currentTag: '',
    init: function (tag) {
        tag = tag || '';
        if (tag) {
            listView.tag.text(tag);
            listView.btnBack.show();
        }
        else {
            listView.tag.text('所有');
            listView.btnBack.hide();
        }
        $.getJSON('api/list_note.php', {tag: tag}, function (data) {
            listView.build(data);
            listView.currentTag = tag;
        });
    },
    item: function (note) {
        var li = $('<li><h2></h2><div class="photo"></div><p class="tags"></p></li>');
        var tags = li.find('.tags');
        var photo = li.find('.photo');

        li.find('h2').text(note.text);

        note.tags.forEach(function (tag) {
            var label = $('<span class="label"></span>').text(tag);
            label.click(function (e) {
                e.stopPropagation();
                listView.init(tag);
            });
            tags.append(label);
        });

        if (note.media) {
            $('<img>').attr('src', 'api/' + note.media).appendTo(photo);
        }

        li.click(function (e) {
            addView.init(note);
            view('add');
        });

        return li;
    },
    build: function (data) {
        var list = listView.list;

        list.empty();
        data.forEach(function (note) {
            var li = listView.item(note);
            list.append(li);
        });
    }
};

listView.btnAdd.click(function (e) {
    addView.init();
    view('add');
});

listView.btnBack.click(function (e) {
    e.preventDefault();
    listView.init();
});




var addView = {
    text: views.add.find('#text'),
    tags: views.add.find('#tags'),
    title: views.add.find('#add-title'),

    fileToggle: views.add.find('#file-toggle'),
    checkFile: views.add.find('#check-file'),

    fileWrapper: views.add.find('#file-wrapper'),
    fileImg: views.add.find('#file-img'),
    photo: views.add.find('#photo'),

    btnSave: views.add.find('#btn-save'),
    btnBack: views.add.find('#btn-back'),
    btnRemove: views.add.find('#btn-remove'),

    hasPhoto: false,
    noteId: 0,

    init: function (note) {
        var img;

        addView.fileToggle.hide();
        addView.fileWrapper.show();
        addView.checkFile[0].checked = false;
        addView.btnRemove.hide();

        addView.noteId = 0;
        addView.text.val('');
        addView.tags.val('');
        addView.fileImg.val('');
        addView.photo.empty();

        addView.btnSave.button('reset');
        addView.hasPhoto = false;

        addView.title.text('添加记录');

        if (note) {
            addView.noteId = note.id;
            addView.text.val(note.text);
            addView.tags.val(note.tags ? note.tags.join(' ') : '');
            addView.fileImg.val('');

            if (note.media) {
                img = $('<img>').attr('src', 'api/' + note.media);
                addView.photo.append(img);
            }

            addView.fileToggle.show();
            addView.btnRemove.show();

            addView.title.text('编辑记录');
        }
    }
};

addView.checkFile.bind('change', function (e) {
    if (e.target.checked) {
        addView.fileWrapper.hide();
    }
    else {
        addView.fileWrapper.show();
    }
});

addView.btnSave.click(function (e) {
    var text, tags, img, fd, file, action;

    function send(action, fd) {
        var xhr = new XMLHttpRequest();

        xhr.addEventListener('load', function (e) {
            addView.btnSave.button('reset');
            view('list');
            listView.init();
        });

        xhr.open('POST', action);
        xhr.send(fd);

        addView.btnSave.button('loading');
    }

    action = addView.noteId ? 'api/update_note.php' : 'api/add_note.php';

    text = addView.text.val();
    tags = addView.tags.val();

    if (!text || !tags) {
        alert('请完整填写文字描述和标签 :-)');
        return;
    }

    fd = new FormData();

    if (addView.noteId) {
        fd.append('id', addView.noteId);

        if (addView.checkFile[0].checked) {
            fd.append('remove_media', true);
        }
    }

    fd.append('text', text);
    fd.append('tags', tags);

    img = addView.photo.find('img')[0];
    file = addView.fileImg[0].files[0];

    if (addView.hasPhoto && file && img) {
        if (file && file.size < 3000000) {
            fd.append('img', file);
            send(action, fd);
        }
        else {
            imgHelper.minify(img, function (blob, ref) {
                fd.append('img', blob);
                send(action, fd);
            });
        }
    }
    else {
        send(action, fd);
    }
});

addView.fileImg.bind('change', function (e) {
    var file;
    var reader;

    file = this.files[0];
    addView.hasPhoto = false;

    if (file) {
        reader = new FileReader;

        reader.onload = function (e) {
            var img = $('<img>').attr('src', e.target.result);

            img.bind('load', function (e) {
                addView.hasPhoto = true;
                addView.btnSave.button('reset');
            });

            img.bind('error', function (e) {
                addView.hasPhoto = false;
                addView.photo.empty();
                addView.btnSave.button('reset');
            });

            addView.photo.empty().append(img);
        };

        addView.btnSave.button('loading');
        reader.readAsDataURL(file);
    }
    else {
            addView.photo.empty();
    }
});

addView.btnBack.click(function (e) {
    view('list');
});

addView.btnRemove.click(function (e) {
    if (addView.noteId) {
        $.get('api/remove_note.php', {id: addView.noteId}, function (result) {
            view('list');
            listView.init(listView.currentTag);
        })
    }
});




view('list');
listView.init();