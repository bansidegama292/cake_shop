function openPopup(id){

    let data = document.getElementById("data-"+id);

    if(!data){
        console.log("No data found for ID:", id);
        return;
    }

    document.getElementById("popup").style.display = "flex";

    // VIEW MODE
    let name = data.querySelector(".name")?.innerHTML || "";
    let price = data.querySelector(".price")?.innerHTML || "";
    let desc = data.querySelector(".desc")?.innerHTML || "";
    let stock = data.querySelector(".badge")?.innerHTML || "";

    document.getElementById("pname").innerHTML = name;
    document.getElementById("pprice").innerHTML = price;
    document.getElementById("pdesc").innerHTML = desc;
    document.getElementById("pstock").innerHTML = stock;

    // EDIT MODE (clean values)
    document.getElementById("eid").value = id;
    document.getElementById("ename").value = name;
    document.getElementById("eprice").value = price.replace(/[^\d]/g,''); // only number
    document.getElementById("estock").value = stock.replace(/[^\d]/g,'');
    document.getElementById("edesc").value = desc;

    // DELETE BUTTON
    document.getElementById("deleteBtn").href = "delete_cake.php?id=" + id;

    // IMAGES
    let imgs = data.querySelectorAll(".images img");

    let mainImg = document.getElementById("mainImg");
    let thumbs = document.getElementById("thumbs");

    thumbs.innerHTML = "";

    if(imgs.length > 0){
        mainImg.src = imgs[0].src;
    } else {
        mainImg.src = "uploads/default.jpg";
    }

    imgs.forEach(img=>{
        let t = document.createElement("img");
        t.src = img.src;

        t.onclick = ()=>{
            mainImg.src = img.src;
        };

        thumbs.appendChild(t);
    });
}