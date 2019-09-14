var number_of_fields = 0
function Add(content){
    number_of_fields++;
    if(content=="text"){
        let node_id = "text"+number_of_fields;
        let node = document.createElement("textarea");
        node.id = node_id
        node.name="content[]"
        let textnode = document.createTextNode("**Enter content here**");
        node.appendChild(textnode);
        document.getElementById("content").appendChild(node);
        $('#'+node_id).summernote();
    }
}