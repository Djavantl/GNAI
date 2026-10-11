const utilityGroups = [
  {
    title: 'Inverter',
    codes: ['wi-flip-horizontal', 'wi-flip-vertical']
  },
  {
    title: 'Rotacionar',
    codes: ['wi-rotate-90', 'wi-rotate-180', 'wi-rotate-270']
  },
  {
    title: 'Largura fixa',
    codes: ['wi-fw']
  }
];

export default function WeatherUtilityClasses(): React.JSX.Element {
  return (
    <section className="iconsPage__weatherUtility">
      <h4 className="iconsPage__sectionTitle iconsPage__sectionTitle--divider page-header header-title">
        <b>Classes utilitárias</b>
      </h4>

      <div className="iconsPage__utilityAlert alert alert-success">
        Use estas classes utilitárias especiais e integradas para inverter, rotacionar ou atribuir uma largura fixa a qualquer ícone.
      </div>

      <div className="iconsPage__utilityRow">
        {utilityGroups.map((group) => (
          <div className="iconsPage__utilityCol" key={group.title}>
            <h5 className="iconsPage__utilityTitle"><b>{group.title}</b></h5>
            {group.codes.map((code) => (
              <p className="iconsPage__utilityCodeLine" key={code}>
                <code>{code}</code>
              </p>
            ))}
          </div>
        ))}
      </div>
    </section>
  );
}
