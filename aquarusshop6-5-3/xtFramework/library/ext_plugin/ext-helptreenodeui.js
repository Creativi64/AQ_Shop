
Ext.tree.HelpTreeNodeUI = Ext.extend(Ext.tree.TreeNodeUI, {

    helpIcon: 'images/icons/help.png',

    renderElements : function(n, a, targetNode, bulkRender){

        this.indentMarkup = n.parentNode ? n.parentNode.ui.getChildIndent() : '';

        var cb = typeof a.checked == 'boolean';

        //console.log(a);
        var qtipTitle = 'Handbuch';
        switch (xt_language)
        {
            case 'en':
                qtipTitle = 'Documentation';
                break;
        }

        var helpBuf = [];
        if(typeof a.url_h == 'string')
        {
            var helpUrl = $.trim(a.url_h);
            if(helpUrl.length>10)
            {
                helpBuf = [
                    "<div style='display: inline-block; position:absolute; right: 5px;'><a href='",helpUrl,"' target='_blank' class='' style='float:right; display:inline-block; vertical-align: middle;' onclick='event.stopPropagation(); '  window.event.cancelBubble = 'true'>",
                    '<i qtip="',qtipTitle,' - ',a.text,'" src="', this.helpIcon, '" alt="Handbuch" class="ux-row-action-item tree-node-help-icon far fa-question-circle" ></i>',
                    "</a></div>"
                ].join('');
                //console.log(helpBuf);
            }
        }

        var href = a.href ? a.href : Ext.isGecko ? "" : "#";
        //console.log(n);
        var buf = ['<li class="x-tree-node"><div ext:tree-node-id="',n.id,'" class="x-tree-node-el x-tree-node-leaf x-unselectable ', a.cls,'" unselectable="on">',
            '<span class="x-tree-node-indent">',this.indentMarkup,"</span>",
            '<img src="', this.emptyIcon, '" class="x-tree-ec-icon x-tree-elbow" />',
            '<i class="x-tree-node-icon',(a.icon ? " x-tree-node-inline-icon" : ""),(a.iconCls ? " "+a.iconCls : ""),'" unselectable="on" ></i>',
            cb ? ('<input class="x-tree-node-cb" type="checkbox" ' + (a.checked ? 'checked="checked" />' : '/>')) : '',
            '<a hidefocus="on" class="x-tree-node-anchor" href="',href,'" tabIndex="1" ',
            a.hrefTarget ? ' target="'+a.hrefTarget+'"' : "", '><span unselectable="on"' + ( n.attributes.textCls ? 'class="' + n.attributes.textCls + '"> ' : '>'), n.text,"</span></a>",

            helpBuf,

            '</div>',
            '<ul class="x-tree-node-ct" style="display:none;"></ul>',
            "</li>"].join('');

        var nel;
        if(bulkRender !== true && n.nextSibling && (nel = n.nextSibling.ui.getEl())){
            this.wrap = Ext.DomHelper.insertHtml("beforeBegin", nel, buf);
        }else{
            this.wrap = Ext.DomHelper.insertHtml("beforeEnd", targetNode, buf);
        }

        this.elNode = this.wrap.childNodes[0];
        this.ctNode = this.wrap.childNodes[1];
        var cs = this.elNode.childNodes;
        this.indentNode = cs[0];
        this.ecNode = cs[1];
        this.iconNode = cs[2];
        var index = 3;
        if(cb){
            this.checkbox = cs[3];
            index++;
        }
        this.anchor = cs[index];
        this.textNode = cs[index].firstChild;

        Ext.QuickTips.init();
    },


});
