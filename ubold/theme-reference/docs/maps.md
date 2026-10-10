# Mapas

As rotas `map-google.html` e `map-vector.html` usam `MapsPage`, que orquestra `CardBox`, `PageBreadcrumb`, `GoogleMap` e `VectorMap`. Os links e estados ativos da sidebar usam os componentes existentes. A implementação usa componentes React e os plugins locais necessários.

As configurações ficam em `pages/MapsPage/MapsPage.google.data.ts` e `pages/MapsPage/MapsPage.vector.data.ts`. Os componentes têm props tipadas, CSS próprio e desmontagem dos plugins. Foram preservados os assets jVectorMap 2.0.2, as nove projeções, os 26 marcadores, as cores, os textos e os oito exemplos GMaps.

## Google Maps

Para configurar uma credencial, crie `react-app/.env.local`:

```dotenv
VITE_GOOGLE_MAPS_API_KEY=sua_chave
```

Reinicie o Vite depois de alterar a configuração. A chave deve pertencer a um projeto habilitado para Maps JavaScript API, com faturamento e restrições de origem apropriadas. Consulte a [documentação do Google](https://developers.google.com/maps/documentation/javascript/get-api-key).

Sem uma chave válida, o Google apresenta `ApiProjectMapError`. As oito instâncias inicializam e o erro não derruba a aplicação React. Navegação cartográfica, Street View e ações sobre mapas exigem uma credencial válida.

O exemplo Routes do HTML legado não contém o botão `start_travel` nem a lista `instructions` esperados pelo script antigo; portanto, continua mostrando apenas o mapa. O seletor Map Types mantém os provedores do legado, inclusive CloudMade, cujo serviço externo não foi validado. URLs de carregamento e tiles usam HTTPS.
