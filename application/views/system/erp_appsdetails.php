<style>
    /** Document **/
    button::-moz-focus-inner {
        padding: 0;
        border: 0
    }

    .content .simplicity-pretty-listbox .success-cases {
        /* border: 1px solid #ECECEC; */
        border-top: none;
        padding: 0;
        margin-top: 20px;
        /* box-shadow: 0 4px 4px -4px rgba(22, 33, 44, .2); */
    }

    .content .simplicity-pretty-listbox .success-cases .text {
        padding: 1%;
        width: 98%;
    }

    .content .simplicity-pretty-listbox .success-cases h3 {
        padding: 0 10px 8px;
        font-size: 18px;
    }

    img.float-right {
        float: right;
        margin-left: 20px;
        padding: 0;
        margin-bottom: 20px;
    }

    img.float-left {
        float: left;
        margin-right: 20px;
        padding: 0;
        margin-bottom: 20px;
    }

    img.float-left, img.float-right {
        box-shadow: 0 8px 8px -8px rgba(22, 33, 44, .2);
        border: 1px solid #ECECEC;
    }

    .document h1.content-title, .document h2.content-title {
        clear: both;
        border-bottom: 1px solid #EEE;
        padding-bottom: 5px;
    }

    /* Sven
    .page h2, .page h1 {
        margin: 15px 0;
    }
    */
    /* Sven
    .page h1 {
      margin-top: 30px;
    }
    */

    .Web-Page-content .page h1,
    .Web-Page-content .document h1 {
        border-bottom: 1px solid #eee;
        clear: both;
        padding-bottom: 5px;
    }

    .document a img:hover {
        opacity: .9;
    }

    .document ul {
        /*color: #d2d6db;*/
        list-style: square;
    }

    .document ul {
        list-style: square url("img/list-icon.png");
    }

    /*
    .document ul li:before {
      background-color: #d2d6db;
      content: "";
      float: left;
      height: 0.5em;
      line-height: 1em;
      margin: 0.65em -1.3em 0;
      width: 0.5em;
    }*/

    .content .table-report {
        font-size: 90%;
        font-family: sans-serif;
        border-style: none;
        border-width: 0;
        border-spacing: 0pt;
        margin-top: 20px;
        border: none;
        box-shadow: 0 2px 2px -2px rgba(22, 33, 44, .2);
        border-bottom: 1px solid #ECEBE2;
    }

    .content .table-report tbody {
        border-width: 0;
    }

    .content .table-report caption, .content .table-report tr th {
        background: #9CB0BD;
        color: white;
        font-style: italic;
        font-weight: normal;
        padding: 8px;
        font-size: 16px;
        /*border-top: 4px solid #83878A;*/
        border-top: 1px solid #fff;
    }

    .content .table-report tr th {
        font-style: normal;
    }

    .content table tbody tr th {
        border-left: none;
    }

    .content .table-report td {
        border: none;
        border-bottom: 1px solid #ECEBE2;
        border-left: 1px solid #ECEBE2;
        background: #fff;
        padding: 5px;
    }

    .content .table-report tr td:last-child {
        border-right: 1px solid #ECEBE2;
    }

    .content .table-report tr:last-child td {
        border-bottom: none;
    }

    .content .table-report tr:hover td {
        background: #FCFCFC;
    }

    .content .table-report tr.accent td {
        background: #F6F6F6;
    }

    .content table.table-report tr td.property {
        background: #F6F6F6;
    }

    .dialog_box {
        color: #989EA2;
    }

    .clear {
        background: none repeat scroll 0 0 rgba(0, 0, 0, 0);
        clear: both;
        margin: 0;
    }

    /** End Document **/

    /** Form  Styles **/

    .field {
        margin: 0 0 10px 0;
    }

    label {
        font-weight: normal;
        font-size: 18px;
    }

    button, input, select, textarea {
        font-size: 100%;
        margin: 0;
        vertical-align: baseline;
    }

    .input input {
        -webkit-transition: all .2s linear;
        transition: all .2s linear;
        -webkit-appearance: none;
        background: #fff;
        border: 2px solid rgba(0, 39, 59, .2);
        padding: 3px 5px;
        margin: 0;
        font-size: 15px;
        height: 25px;
        margin-top: 5px;
    }

    .input input:focus, .input textarea:focus {
        border: 2px solid #cbd2d6;
        border: 2px solid rgba(0, 39, 59, .35);
        outline: none;
    }

    .input textarea {
        color: #3f4549;
        cursor: text;
        border: 0;
        display: block;
        padding: 5px 6px 5px;
        margin: 0;
        margin-top: 5px;
        min-height: 44px;
        height: auto;
        line-height: 1.4;
        font-size: 14px;
        overflow: auto;
        -webkit-transition: all .15s ease-in-out;
        transition: all .15s ease-in-out;
        border: 2px solid rgba(0, 39, 59, .2);
    }

    .bt-med, .formbt {
        border: 0;
        color: white;
        padding: 6px 20px !important;
        line-height: 19px;
        background-color: #AAB0B4;
        cursor: pointer;
        -webkit-box-shadow: inset 0 0px 20px rgba(0, 0, 0, 0.11);
        -moz-box-shadow: inset 0 0px 20px rgba(0, 0, 0, 0.11);
        box-shadow: inset 0 0px 20px rgba(0, 0, 0, 0.11);
        transition: all 300ms ease-in-out;
        -moz-transition: all 300ms ease-in-out;
        -webkit-transition: all 300ms ease-in-out;
        -o-transition: all 300ms ease-in-out;
        border: 1px solid rgba(0, 39, 59, .2);
    }

    .bt-med:hover, .formbt:hover {
        background-color: #1C76BB;
        transition: all 200ms ease-in-out;
        -moz-transition: all 200ms ease-in-out;
        -webkit-transition: all 200ms ease-in-out;
        -o-transition: all 200ms ease-in-out;
    }

    /** End Form Styles **/

    /** Application Page **/

    .content-apps {
        margin: 20px 0;
    }

    .content-apps h1 {
        font-weight: normal;
        font-size: 25px;
        line-height: 33px;
    }

    .apps-info {
        margin-left: 30px;
        /* margin-left: 10px; */
        /* width: 200px; */
        /* min-height: 300px; */
    }

    .apps-description {

        color: #404040;
        margin-bottom: 20px;
    }

    .apps-logo {
        text-align: left;
        border: none;
    }

    .apps-logo img {
        max-width: 180px;
        margin-bottom: 20px;
    }

    .apps-download-link {
        margin-top: 10px;
        width: 150px;
    }

    .apps-download-link a {
        text-transform: capitalize;
    }

    .apps-info h3 {
        color: #474747;
        font-size: 16px;
        font-weight: normal;
        margin: 0;
    }

    .apps-info h3 span {
        color: #808080;
        font-size: 13px;
    }

    .apps-info h2 {
        color: #808080;
        font-size: 14px;
        line-height: 16px;
        margin: 0;
    }

    .apps-categories {
        /*margin: 20px 0;*/
    }

    .separator {
        margin: 5px 0 20px;
        border-top: 1px #BABABA solid;
    }

    .app-category {
        margin-top: 15px;
    }

    .app-category a, .apps-description a {
        display: inline;
        padding: 0;
        text-align: left;
        text-transform: none;
        text-decoration: none;
        letter-spacing: 0.15em;
        font-weight: normal;
        line-height: 18px;
        background: none;
        font-size: 15px;
        padding: 0;
        color: #1C76BB;
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
        box-shadow: none;
    }

    .app-category a {
        font-size: 14px;
    }

    .app-category a:hover, .apps-description a:hover {
        background: none;
        color: #1C76BB;
        transition: none;
        -moz-transition: none;
        -webkit-transition: none;
        -o-transition: none;
    }

    .apps-description h2 {
        font-size: 24px;
        border-top: 1px solid #BABABA;
        padding-top: 15px;
        padding-bottom: 10px;
        color: #404040;
    }

    .block-text p {
        margin: 0;
        margin-bottom: 10px;
    }

    .block-text p:last-child {
        margin-bottom: 0
    }

    .app-gallery {
        position: relative;
        top: 0px;
        left: 0px;
        width: 100%;
        /*   height: 500px;  */
        overflow: hidden;
        background: transparent; /*#5A5A5A;*/
        margin-bottom: 0px;
    }

    .apps-description .app-gallery .galleria-theme-classic,
    .apps-description .app-gallery {
        background: white;
    }

    .block-text a:hover {
        text-decoration: underline;
    }

    a.button {
        display: block;
        padding: 2px;
        text-align: center;
        text-transform: capitalize;
        text-decoration: none;
        letter-spacing: 0.15em;
        font-size: 16px;
        font-weight: normal;
        line-height: 18px;
        background: #AAB0B4;
        padding: 10px 0;
        color: white;
        -webkit-box-shadow: inset 0 0px 20px rgba(0, 0, 0, 0.11);
        -moz-box-shadow: inset 0 0px 20px rgba(0, 0, 0, 0.11);
        box-shadow: inset 0 0px 20px rgba(0, 0, 0, 0.11);
        transition: all 300ms ease-in-out;
        -moz-transition: all 300ms ease-in-out;
        -webkit-transition: all 300ms ease-in-out;
        -o-transition: all 300ms ease-in-out;
    }

    a.button:hover {
        text-decoration: none;
        background-color: #1C76BB;
    }

    /** End Application Page **/

    /** Forum - Discussion Post **/
    .discussion-thread {
        margin: 20px 0;
    }

    .discussion-thread-button {
        margin: 10px 0 40px;
    }

    /* =========== Sven: BUTTONS (are a mess!!! ) ============== */
    a.share-button {
        display: inline;
        font-size: 14px;
        border: 1px solid #CCCCCC;
        /* Sven: we don't introduce new colors */
        /* background: #E1E1E1; */
        background: rgb(170, 176, 180);
        /* color: #6D6D6D; */
        color: #ffffff;
        line-height: 1;
        margin-right: 10px;
        padding: 6.75px 12.5px;
        text-transform: none;
        box-shadow: inset 0 0px 20px rgba(0, 0, 0, 0.11);
    }

    a.share-button span {
        display: inline !important;
        font-weight: bold;
        margin-left: 30px;
        padding-left: 11.5px;
    }

    /* Sven: Sometimes the button is inside a form, so can only push like this,
    but only there is an "add [some]" button before and user is not logged in... */
    .add_new_blog_post ~ div a.share-button.rss-feed {
        top: -41px;
        left: 14em;
    }

    .discussion-thread-button a.share-button.rss-feed {
        top: 0;
        left: 0;
    }

    a.share-button.rss-feed {
        position: relative;
        padding-left: 3em;
        padding-right: 1em;
        line-height: 1;
        font-size: 14px;
        padding-top: 8px;
        padding-bottom: 8px;
    }

    /* Sven: whoever put the image there */
    a.share-button.rss-feed > img {
        display: none;
    }

    /* Sven: finally, switch all to pseudo */
    a.share-button.rss-feed > div {
        display: none;
    }

    /* Sven: all images go on :pseudo, same everywhere */
    a.share-button.rss-feed:before {
        content: "";
        background: url(img/rss-white.png) left center no-repeat;
    }

    /* Sven: button without text...? */
    a.share-button.rss-feed:after {
        content: "RSS Feed";
        font-weight: bold;
    }

    /* Tristan: button without text...? */
    a.share-button.rss-feed.data-content:after {
        content: attr(data-content) "";
        font-weight: bold;
    }

    a.rss-feed div {
        background: url(img/rss-white.png) left center no-repeat;
        display: inline;
    }

    a.rss-feed span {
        /* border-left: 1px solid #6D6D6D; */
        border-left: 1px solid #ffffff;
    }

    a.rss-feed:hover {
        /* Sven: we don't introduce new colors */
        /* background: #CCCCCC; */
        background: #1c76bb;
    }

    /* Sven: applying to multiple buttons */
    .add_new_blog_post > a.button,
    a.post-message {
        border: 1px solid #2455B4;
        /* Sven: we don't introduce new colors */
        /* background: #184FBE; */
        background: #1c76bb;
        color: #FFF;
    }

    /* Sven: we don't have div & span inside the a.button sometimes ... */
    .add_new_blog_post > a.button {
        display: inline-block;
        font-weight: bold;
        font-size: 14px;
        letter-spacing: normal;
        padding-left: 3em;
        padding-right: 1em;
        line-height: 1;
        position: relative;
    }

    .add_new_blog_post > a.button:before {
        content: "";
        background: url(img/add-create.png) left center no-repeat;
    }

    .discussion-thread-button > a.button:before {
        content: none;
        background: 0 none;
    }

    .add_new_blog_post > a.button:before,
    a.share-button.rss-feed:before {
        position: absolute;
        width: 20px;
        height: 20px;
        bottom: .5em;
        left: .5em;
        padding-left: 5px;
        border-right: 1px solid #fff;
    }

    a.post-message div {
        background: url(img/add-create.png) left center no-repeat;
        display: inline !important;
    }

    a.post-message span {
        border-left: 1px solid #fff;
    }

    .add_new_blog_post > a.button:hover,
    a.post-message:hover {
        /* Sven: we don't introduce new colors */
        /* background: #254178; */
        background: #1c76bb;
    }

    a.post-message:hover span {
        border-left: 1px solid #fff;
    }

    .discussion-thread-listbox .listbox-body {
        margin: 20px 0;
        width: 100%;
        color: #4E4E4E;
    }

    .discussion-thread-listbox .listbox-body dd {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .discussion-thread-listbox .listbox-body h1 {
        font-weight: normal;
        font-size: 25px;
        margin: 10px 0;
        padding: 10px 0;
    }

    .thread {
        padding: 0 0 20px;
        display: table;
        width: auto;
    }

    .thread-user {
        display: table-cell;
        width: 140px;
        font-size: 12px;
    }

    .thread-content-separator {
        display: table-cell;
        max-width: 200px;
        vertical-align: middle;
    }

    .thread-content-separator div {
        padding: 40px 0;
        margin: 0 10px;
        border-right: 1px solid #D8D8D8;
    }

    .table-row {
        display: table-row;
        width: auto;
        clear: both;
    }

    .thread-content {
        display: table-cell;
    }

    .thread-content p {
        color: #363738;
    }

    .discussion-thread-listbox .listbox-table {
        margin: 0;
        border: none !important;
        border-collapse: collapse;
    }

    .discussion-thread-listbox .listbox-table td::after {
        clear: both;
    }

    .discussion-thread-listbox .listbox-table td {
        border: none !important;
        padding: 0;
        padding-top: 10px;
    }

    .post-content {
        position: relative;
        margin-bottom: 20px;
        -webkit-transition: all .2s ease-in-out;
        transition: all .2s ease-in-out;
    }

    .post-content::before, .post-content::after {
        display: table;
        content: "";
        line-height: 0;
    }

    .post-content::after {
        clear: both;
    }

    .avatar {
        margin-right: 12px;
    }

    .avatar {
        float: left;
    }

    .avatar .user {
        display: block;
        position: relative;
        z-index: 100;
        background: rgba(110, 115, 117, .2);
    }

    .post-profile::before {
        display: table;
        content: "";
        line-height: 0;
    }

    .post-profile .user {
        display: block;
        position: relative;
        z-index: 100;
        background: rgba(110, 115, 117, .2);
        width: 128px;
    }

    .avatar img {
        display: block;
        width: 48px;
        height: 48px;
    }

    .post-profile img {
        display: block;
        width: 128px;
        height: 128px;
    }

    .post-content .post-data {
        overflow: hidden;
    }

    .post-content div.discussion-post-header {
        display: block;
        color: #777;
        line-height: 1;
        font-size: 13px;
        padding-right: 46px;
        margin-bottom: 3px;
        background: transparent;
    }

    div.discussion-post-header .post-author {
        font-weight: 700;
    }

    div.discussion-post-header .title {
        float: none;
        margin-bottom: 5px;
    }

    div.discussion-post-header .title a {
        font-weight: bold;
        font-size: 15px;
    }

    .post-content .bullet {
        padding: 0 4px;
        font-size: 75%;
        color: #CCC;
        line-height: 1.4;
    }

    .post-meta {
        display: inline-block;
    }

    .post-content div.discussion-post-header .post-time {
        font-weight: 500;
        font-size: 12px;
        color: #A5B2B9;
        color: rgba(0, 39, 59, .35);
    }

    .discussion-post-body-container p:last-child, .thread-content p {
        margin: 0;
    }

    .discussion-post-body-container p, .thread-content p {
        line-height: 21px;
        margin: 0 0 15px;
    }

    .discussion-post-body-container {
        padding-top: 10px;
    }

    .discussion-post-body-container blockquote {
        background: #FAFAFA;
        border-left: 6px solid #e7e9ec;
        padding: 5px 5px 5px 16px;
        font-style: italic;
    }

    .discussion-post-actions {
        color: #768187;
        color: rgba(29, 47, 58, .6);
        margin: 10px 0 0;
        font-size: 13px;
        border-top: 1px #E2E2E2 solid;
        line-height: 20px;
        padding: 5px 0 0;
        width: 100%;
    }

    .discussion-post-actions a {
        color: #768187;
    }

    .discussion-post-actions a:hover {
        color: #B7B8B9;
    }

    .discussion-post-actions button.discussion-post-action-button {
        border: none;
        outline: none;
        margin: 0;
        background: url(img/post-reply.png) right no-repeat;
        padding: 0 17px 0 0 !important;
        color: #768187;
        cursor: pointer;
        font-size: 14px;
        transition: all 300ms ease-in-out;
        -moz-transition: all 300ms ease-in-out;
        -webkit-transition: all 300ms ease-in-out;
        -o-transition: all 300ms ease-in-out;
    }

    .discussion-post-actions button.discussion-post-action-button:hover {
        transition: all 200ms ease-in-out;
        -moz-transition: all 200ms ease-in-out;
        -webkit-transition: all 200ms ease-in-out;
        -o-transition: all 200ms ease-in-out;
        opacity: .7;
    }

    .post-header::before {
        display: table;
        content: "";
        line-height: 0;
    }

    .post-header::after {
        clear: both;
    }

    .discussion-thread-listbox .listbox-head-title {
        border-bottom: 2px solid;
        border-color: #359BE9;
        padding: 0;
        font-weight: bold;
        float: left !important;
        font-size: 16px;
        color: #868686;
        line-height: 22px;
    }

    .discussion-thread-listbox .listbox-head-title:hover {
        border-color: #0CF;
    }

    .discussion-thread-listbox .listbox-head-title a, .discussion-thread-listbox .listbox-head-title a:hover {
        color: #868686;
    }

    .discussion-thread-listbox .listbox-head-navigation {
        border-bottom: 2px solid;
        border-color: #EAEDEE;
        border-color: rgba(0, 39, 59, .08);
        padding: 0;
        color: #868686;
        width: 100% !important;
        float: none !important;
        text-align: right;
        line-height: 22px;
    }

    .discussion-thread-listbox .listbox-head-navigation div.listbox-header-box {
        padding: 0;
        float: none;
    }

    div.listbox-head-spacer {
        background: none;
        height: 0px;
        width: 0px;
    }

    .icon-reply {
        background: url(img/reply.png) left center no-repeat;
    }

    .reply-message {
        margin: 5px 0;
        font-size: 14px;
        font-weight: bold;
    }

    .reply-message a:hover, table.listbox td.listbox-table-data-cell a.discussion-post-title:hover {
        opacity: 0.7;
    }

    .reply-message a, table.listbox td.listbox-table-data-cell a.discussion-post-title {
        color: #868686;
        opacity: 1;
    }

    .reply-message a span {
        padding-left: 20px;
    }

    /** End Forum - Discussion Post **/

    /** Blog Post **/

    .blog_post {
        padding: 10px;
        margin: 20px 0;
        /*border-radius: 3px; */
        max-width: 800px;
        /* Remove if not useful
            border: 1px solid rgb(212, 214, 216);
          box-shadow: 0 4px 4px -4px rgba(22, 33, 44, .2);
          */
    }

    .blog_post:hover {
        /*background: rgb(243, 244, 245);*/
    }

    .blog_post::after {
        display: table;
        content: "";
        line-height: 0;
    }

    .blog_post::after {
        clear: both;
    }

    .post_head h1 {
        margin: 0;
        margin-bottom: 5px;
        font-size: 23px !important;
    }

    .post_head h1 a {
        /*color: rgb(82, 84, 85);*/
        text-decoration: none;
    }

    .blog_post h1 a:hover {
        text-decoration: none;
    }

    .post_head a:hover {
        text-decoration: underline;
    }

    .post_head {
        color: #8B8B8B;
        font-size: 12px;
    }

    .post_author {
        font-weight: bold;
    }

    .post_body {
        padding: 15px 0 0;
        min-height: 50px;
        color: #404040;
    }

    .post_bottom {
        color: rgb(139, 139, 139);
        padding-top: 10px;
        font-size: 14px;
    }

    .post_bottom a {
        font-weight: 700;
    }

    .post_bottom a.post_comments {
        background: url(img/comments-icon.png) left center no-repeat;
        padding-left: 28px;
        color: #868686;
    }

    .post_bottom > a.post_comments:hover {
        color: #B3BABD;
    }

    .post_bottom img {
        margin-left: 5px;
    }

    /** End Blog Post **/

    /** ListBox **/

    div.listbox-body {
        float: left;
        width: 100%;
        /*box-shadow: 1px 1px 1px #9A9A9B;*/
    }

    div.listbox-content {
        float: left;
        width: 100%;
        color: #5B5B5C;
    }

    div.listbox-head-content {
        border-right: non;
        border-top: none;
        height: 25px;
        margin-left: 10px;
        padding-top: 10px;
        padding-right: 5px;
        width: auto;
    }

    table.listbox tr.listbox-label-line th.listbox-table-header-cell {
        font-weight: bold;
        color: #676767;
        padding: 0 10px;
        color: #fff;
        line-height: 34px;
    }

    table.listbox td.listbox-table-data-cell a {
        color: #1D669E;
    }

    /* Sven: fix listbox headline */
    div.listbox-title {
        font-weight: normal;
        color: #9D968D;
    }

    table.listbox {
        border-collapse: collapse;
        width: 100%;
        border: none;
        margin-bottom: 0em;
        /*border:1px solid #8F8F8F;*/
    }

    table.listbox th,
    table.listbox td {
        text-align: left;
        vertical-align: top;
        border: none;
        /*border-bottom:1px solid #8F8F8F;*/
        padding: .3em;
        padding-left: 10px;
        padding-right: 1px;
        font-weight: normal;
    }

    /* Sven: no margin on search cells */
    table.listbox th input,
    table.listbox td input {
        margin: 0;
        padding-left: 0;
        padding-right: 0;
        width: 100%;
    }

    .content table tbody tr td {
        border: none;
    }

    table.listbox tr.listbox-label-line {
        color: #001730;
        border-top: none;
        /* background: none repeat scroll 0 0 #616E75; */
        background: none repeat scroll 0 0 rgb(170, 176, 180)
    }

    html table.listbox tr.listbox-search-line {
        background-color: rgb(228, 235, 241)
    }

    table.listbox tr.DataA {
        color: inherit;
        background-color: #F8F8F8;
    }

    table.listbox th img.sort-button-arrow {
        height: 13px;
        margin: 0;
        padding: 0;
        width: 13px;
    }

    .listbox-head-spacer {
        width: 0 !important;
    }

    .listbox-head-content {
        margin: 0 !important;
    }

    .discussion-thread-listbox table.listbox tr.DataA, .simplicity-pretty-listbox table.listbox tr.DataA {
        background-color: transparent;
    }

    table.listbox tr.listbox-search-line th input {
        padding-bottom: 5px;
    }

    .form-control {

        font-size: 14px;
    }

    .help-block {
        display: block;
        margin-top: 5px;
        margin-bottom: 10px;
        color: #737373;
        font-size: 12px;
    }

    /** End ListBox **/

</style>
<?php
$CI = get_instance();
$navigationMenuID = $extra;
$companyID = current_companyID();
$detail = $CI->db->query("SELECT srp_erp_navigationmenus.*,if(moduleID!='',1,0) as added FROM srp_erp_navigationmenus LEFT JOIN `srp_erp_moduleassign` on srp_erp_navigationmenus.navigationMenuID=srp_erp_moduleassign.navigationMenuID WHERE srp_erp_navigationmenus.navigationMenuID={$navigationMenuID} AND companyID=$companyID")->row_array();
$attachment = $CI->db->query("SELECT * FROM srp_erp_moduleattachment WHERE navigationMenuID ={$navigationMenuID}")->result_array();
$addon = $CI->db->query("SELECT srp_erp_navigationmenus.*,if(moduleID!='',1,0) as added FROM `srp_erp_navigationmenus` LEFT JOIN `srp_erp_moduleassign` on srp_erp_navigationmenus.navigationMenuID=srp_erp_moduleassign.navigationMenuID AND  companyID={$companyID} WHERE isAddon >= 2  ORDER BY added ,sortOrder  asc")->result_array();
?>
<?php echo head_page('', false); ?>

<div id="filter-panel" class="collapse filter-panel"></div>
<div class="row">
    <div class="col-md-2 offset-sm-1 ">
        <div class="apps-info">
            <div class="apps-logo"><img width="120px" src="<?php echo base_url('images/erp-logo.png'); ?>"></div>
            <div class="separator"></div>
            <?php if ($detail['added'] == 1) { // check module is assigned to the user if it is assigned we will it is added ?>
                <div class="apps-download-link"><a disabled="" class="button" href="#">Added</a></div>
            <?php } else { ?>
                <div class="apps-download-link"><a class="button" onclick="submitQuote()" href="#">Get Quote</a></div>
            <?php } ?>
            <div class="apps-categories">
                <div class="app-category">
                    <h2>Current Version</h2>
                    <h3>2.6</h3>
                </div>
                <div class="app-category">
                    <h2>Category</h2>
                    <h3><?php echo $detail['addonDescription'] ?></h3>
                </div>
                <div class="app-category">
                    <h2>Type</h2>
                    <h3><?php echo ($detail['isAddon'] == 1) ? 'Core Module' : 'Addon'; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-10 offset-sm-1">
        <div class="apps-description">

            <?php if (!empty($attachment)) { ?>
                <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                    <!-- Indicators -->
                    <ol class="carousel-indicators">
                        <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                        <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                    </ol>
                    <!-- Wrapper for slides -->
                    <div class="carousel-inner" role="listbox">
                        <?php
                        $i = 1;
                        foreach ($attachment as $value) {
                            $active = '';
                            if ($i == 1) {
                                $active = 'active';
                            }
                            ?>
                            <div style="padding-left: 120px;padding-right: 120px" class="item <?php echo $active ?>">
                                <img style="max-width: 100%;"
                                     src="<?php echo base_url('images/module/' . $value['file']); ?>"
                                     alt="Slide <?php echo $i ?>">
                                <div class="carousel-caption">
                                    <?php echo $value['description'] ?>
                                </div>
                            </div>
                            <?php
                            $i++;
                        } ?>
                    </div>
                    <!-- Controls -->
                    <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            <?php }
            echo $detail['addonDetails'] ?>
        </div>
    </div>
</div>

<div id="quotemodal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="width: 400px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Request a Quote</h4>
            </div>

            <div class="modal-body">
                <form id="quoteform" name="quoteform" class="form-horizontal">

                    <?php
                    $currentuserID = current_userID();
                    $countrys = load_country_drop();
                    $name = fetch_employeeNo($currentuserID) ?>
                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="textinput"></label>
                        <div class="col-md-8">
                            <input id="requestName" readonly value="<?php echo $name['Ename2'] ?>" name="requestName"
                                   type="text" placeholder="   Your Name" class="form-control input-md">

                        </div>
                    </div>

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="textinput"></label>
                        <div class="col-md-8">
                            <input id="companyName" readonly value="<?php echo current_companyName(); ?>"
                                   name="companyName" type="text" placeholder="   Company Name"
                                   class="form-control input-md">

                        </div>
                    </div>

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="textinput"></label>
                        <div class="col-md-8">
                            <input id="phoneNumber" name="phoneNumber" value="<?php echo $name['EcMobile'] ?>"
                                   type="text" placeholder="   Phone Number" class="form-control input-md">

                        </div>
                    </div>

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="textinput"></label>
                        <div class="col-md-8">
                            <input id="emailID" name="emailID" value="<?php echo $name['EEmail'] ?>" type="text"
                                   placeholder="   Email ID" class="form-control input-md">

                        </div>
                    </div>

                    <!-- Select Basic -->

                    <div class="form-group">
                        <label class="col-md-2 control-label" for="selectbasic"></label>
                        <div class="col-md-8">
                            <select id="erpSystem" name="erpSystem" class="form-control">
                                <option></option>
                                <?php if ($addon) {
                                    foreach ($addon as $value) {
                                        $select = '';
                                        if ($navigationMenuID == $value['navigationMenuID']) {
                                            $select = 'selected';
                                        }
                                        ?>
                                        <option <?php echo $select ?>
                                                value="<?php echo $value['navigationMenuID'] ?>"><?php echo $value['addonDescription'] ?></option>

                                        <?php
                                    }
                                } ?>
                            </select>
                        </div>
                    </div>

                    <!--  -->
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="selectbasic"></label>
                        <div class="col-md-8">
                            <select id="country" name="country" class="form-control">
                                <option></option>
                                <?php foreach ($countrys as $country) {
                                    $select = '';
                                    if ($name['Nid'] == $country['countryID']) {
                                        $select = 'selected';
                                    }
                                    ?>
                                    <option <?php echo $select ?>
                                            value="<?php echo $country['CountryDes']; ?>"><?php echo $country['CountryDes'] . ' | ' . $country['countryShortCode']; ?></option>
                                <?php }; ?>
                            </select>
                        </div>
                    </div>


                    <!--  -->
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="selectbasic"></label>
                        <div class="col-md-8">
                            <select id="aboutUs" name="aboutUs" class="form-control">
                                <option></option>
                                <option value="1">Employer</option>
                                <option value="2">Internet Search</option>
                                <option value="3">Social Network</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label" for="selectbasic"></label>
                        <div class="col-md-8">
                        <span class="help-block"> By checking this box and submitting my information to <?php echo SYS_NAME ?>
                            ,
                                I consent to being contacted by <?php echo SYS_NAME ?> with respect to my
                                enquiry as well as for marketing purposes, In addition , I agree
                                   to the Terms of Use and Privacy Policy </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label" for="terms"></label>
                        <div class="col-md-8">
                            <label class="checkbox-inline" for="terms-0" style="font-size: 12px">
                                <input type="checkbox" name="terms" id="terms-0" value="1"> I Agree
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label" for="terms"></label>
                        <div class="col-md-8">
                            <button type="button" id="save_btn" onclick="submitq()" class="btn btn-primary">Submit
                            </button>
                        </div>
                    </div>


                </form>
            </div>
            <div class="modal-footer">


            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script>

    function submitq() {
        if ($('#terms-0').is(':checked')) {

            var data = $('#quoteform').serializeArray();
            data.push({'name': 'GLAutoID', 'value': 1});
            data.push({'name': 'erpSystemDescription', 'value': $("#erpSystem option:selected").text()});
            data.push({'name': 'aboutUsDescription', 'value': $("#aboutUs option:selected").text()});

            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: data,
                url: "<?php echo site_url('Employee/requestQuote'); ?>",
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    myAlert(data[0], data[1]);
                    stopLoad();
                    if (data[0] == 's') {
                
                    }


                    refreshNotifications(true);
                }, error: function () {
                    alert('An Error Occurred! Please Try Again.');
                    stopLoad();
                    refreshNotifications(true);
                }
            });

        }
        else {
            myAlert('e', 'Please indicate that you have read and agree to the Terms and condition');
        }
    }

    function submitQuote() {
        $('#quotemodal').modal('show');
        $("#erpSystem").select2({
            placeholder: "Which ERP system you want ?",
            allowClear: true
        });
        $("#country").select2({
            placeholder: "Your Country",
            allowClear: true
        });
        $("#aboutUs").select2({
            placeholder: "How did you hear about us ?",
            allowClear: true
        });
    }
</script>
