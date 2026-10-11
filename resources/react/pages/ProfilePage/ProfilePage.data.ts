import type { ProfileData } from "./ProfilePage.types";
export const profileData: ProfileData = {
  name: "Chadengle",
  location: "Nova York, Estados Unidos",
  avatar: "/assets/react/images/users/avatar-1.jpg",
  details: [
    {
      label: "Nome completo",
      value: "Johnathan Deo",
    },
    {
      label: "Celular",
      value: "(123) 123 1234",
    },
    {
      label: "E-mail",
      value: "johnathan@Interface.com",
    },
    {
      label: "Localização",
      value: "EUA",
    },
  ],
  biography: [
    "Lorem Ipsum é um texto de exemplo usado na indústria gráfica e de composição. Ele se tornou o padrão desde o século XVI, quando um impressor reuniu e reorganizou tipos para criar um livro de amostras. O texto sobreviveu a cinco séculos e à chegada da composição eletrônica, mantendo suas características originais.",
    "Mesmo com a chegada da composição eletrônica, suas características permaneceram praticamente iguais.",
    "Sua popularidade cresceu na década de 1960 com as folhas Letraset que traziam trechos de Lorem Ipsum. Mais recentemente, programas de editoração como o Aldus PageMaker também passaram a incluir versões desse texto.",
  ],
  skills: [
    {
      name: "AngularJs",
      value: 60,
      tone: "primary",
    },
    {
      name: "Javascript",
      value: 90,
      tone: "pink",
    },
    {
      name: "Wordpress",
      value: 80,
      tone: "purple",
    },
    {
      name: "HTML5 & CSS3",
      value: 95,
      tone: "info",
    },
  ],
  mapSrc:
    "https://maps.google.co.in/maps?f=q&source=s_q&hl=en&geocode=&q=Google+India+Bangalore,+Bennigana+Halli,+Bangalore,+Karnataka&aq=0&oq=google+&sll=9.930582,78.12303&sspn=0.192085,0.308647&ie=UTF8&hq=Google&hnear=Bennigana+Halli,+Bangalore,+Bengaluru+Urban,+Karnataka&t=m&ll=12.993518,77.660294&spn=0.012545,0.036006&z=15&output=embed",
};
