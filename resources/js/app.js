import "./bootstrap";
import Search from "./search";
import Chat from "./chat";
import Profile from "./profile";

if (document.querySelector(".header-search-icon")) {
    new Search();
}

if (document.querySelector(".header-chat-icon")) {
    new Chat();
}

if (document.querySelector(".profile-nav")) {
    new Profile();
}
