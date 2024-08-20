import { handleSidebar, handleDisplayCurrentTime, handleNotification, handleTdColor } from "./dashboards-main.js";

document.addEventListener("DOMContentLoaded", () => {
  handleTdColor();
  handleSidebar();
  handleDisplayCurrentTime();
  handleNotification();
});