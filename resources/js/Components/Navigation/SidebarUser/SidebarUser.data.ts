import type { SidebarUserDetails } from "./SidebarUser.types";

export const sidebarUser: SidebarUserDetails = {
  name: "John Doe",
  role: "Administrador",
  avatar: "/assets/gnai/images/users/avatar-1.jpg",
};

export const sidebarUserActions = [
  { label: "Perfil", icon: "md md-face-unlock" },
  { label: "Configurações", icon: "md md-settings" },
  { label: "Tela de bloqueio", icon: "md md-lock" },
  { label: "Sair", icon: "md md-settings-power" },
];
