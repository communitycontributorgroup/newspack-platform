jQuery(document).ready((function(t){t("#podcasting-enclosure-button").click((function(e){e.preventDefault();var a,n=t(this),i=t("input#podcasting-enclosure-url");a||((a=wp.media.frames.file_frame=wp.media({title:n.data("modalTitle"),button:{text:n.data("modalButton")},library:{type:"audio"},multiple:!1})).off("select"),a.on("select",(function(){var t=a.state().get("selection").first();i.val(t.get("url"))}))),a.open()}))}));