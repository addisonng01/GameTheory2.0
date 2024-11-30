//let cardType = "BLACK";
let cardType = document.getElementById("cardType").value;
let card = document.getElementById("chosenCard");
let redCard = "..\\..\\Img\\kingHearts.svg";
let redCardAlt = "red card image"
let blackCard = "..\\..\\Img\\kingSpades.svg";
let blackCardAlt = "black card image"

//if ($result->num_rows == 0) {
if (cardType == "RED"){
    card.src = redCard;
    card.alt = redCardAlt;
}
else if (cardType == "BLACK"){
    card.src = blackCard;
    card.alt = blackCardAlt;
}