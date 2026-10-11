export default function FontAwesomeExamples(): React.JSX.Element {
  return (
    <div className="iconsPage__examples">
      <h4 className="iconsPage__sectionTitle iconsPage__sectionTitle--pageHeader page-header m-t-0 header-title"><b>Exemplos</b></h4>

      <div className="iconsPage__examplesRow">
        <div className="iconsPage__exampleCol">
          <div className="iconsPage__exampleBlock">
            <h5 className="iconsPage__exampleTitle text-muted text-uppercase m-b-20"><b>Ícones maiores</b></h5>
            <i className="fa fa-camera-retro fa-lg" /> fa-lg <br />
            <i className="fa fa-camera-retro fa-2x" /> fa-2x <br />
            <i className="fa fa-camera-retro fa-3x" /> fa-3x <br />
            <i className="fa fa-camera-retro fa-4x" /> fa-4x <br />
            <i className="fa fa-camera-retro fa-5x" /> fa-5x <br />
          </div>
        </div>

        <div className="iconsPage__exampleCol">
          <div className="iconsPage__exampleBlock">
            <h5 className="iconsPage__exampleTitle text-muted text-uppercase m-b-20"><b>Ícones com largura fixa</b></h5>
            <div className="iconsPage__listGroup list-group">
              <a className="iconsPage__listGroupItem list-group-item" href="#"><i className="fa fa-home fa-fw" />&nbsp; Início</a>
              <a className="iconsPage__listGroupItem list-group-item" href="#"><i className="fa fa-book fa-fw" />&nbsp; Biblioteca</a>
              <a className="iconsPage__listGroupItem list-group-item" href="#"><i className="fa fa-pencil fa-fw" />&nbsp; Aplicações</a>
              <a className="iconsPage__listGroupItem list-group-item" href="#"><i className="fa fa-cog fa-fw" />&nbsp; Configurações</a>
            </div>
          </div>
        </div>

        <div className="iconsPage__exampleCol">
          <div className="iconsPage__exampleBlock">
            <h5 className="iconsPage__exampleTitle text-muted text-uppercase m-b-20"><b>Ícones em listas</b></h5>
            <ul className="fa-ul iconsPage__faList">
              <li><i className="fa-li fa fa-check-square" />Ícones de lista</li>
              <li><i className="fa-li fa fa-check-square" />podem ser usados</li>
              <li><i className="fa-li fa fa-spinner fa-spin" />como marcadores</li>
              <li><i className="fa-li fa fa-square" />em listas</li>
            </ul>
          </div>
        </div>
      </div>

      <div className="iconsPage__examplesRow">
        <div className="iconsPage__exampleCol">
          <div className="iconsPage__exampleBlock">
            <h5 className="iconsPage__exampleTitle text-muted text-uppercase m-b-20"><b>Ícones com borda e alinhamento</b></h5>
            <i className="fa fa-quote-left fa-3x fa-pull-left fa-border" />
            ...amanhã correremos mais rápido, esticaremos os braços mais longe...
            E então, em uma bela manhã, seguimos em frente, barcos contra a
            corrente, arrastados sem cessar para o passado.
          </div>
        </div>

        <div className="iconsPage__exampleCol">
          <div className="iconsPage__exampleBlock">
            <h5 className="iconsPage__exampleTitle text-muted text-uppercase m-b-20"><b>Rotacionados e invertidos</b></h5>
            <i className="fa fa-shield" /> normal<br />
            <i className="fa fa-shield fa-rotate-90" /> fa-rotate-90<br />
            <i className="fa fa-shield fa-rotate-180" /> fa-rotate-180<br />
            <i className="fa fa-shield fa-rotate-270" /> fa-rotate-270<br />
            <i className="fa fa-shield fa-flip-horizontal" /> fa-flip-horizontal<br />
            <i className="fa fa-shield fa-flip-vertical" /> icon-flip-vertical
          </div>
        </div>

        <div className="iconsPage__exampleCol">
          <div className="iconsPage__exampleBlock">
            <h5 className="iconsPage__exampleTitle text-muted text-uppercase m-b-20"><b>Ícones empilhados</b></h5>
            <span className="fa-stack fa-lg">
              <i className="fa fa-square-o fa-stack-2x" />
              <i className="fa fa-twitter fa-stack-1x" />
            </span>
            fa-twitter sobre fa-square-o<br />
            <span className="fa-stack fa-lg">
              <i className="fa fa-circle fa-stack-2x" />
              <i className="fa fa-flag fa-stack-1x fa-inverse" />
            </span>
            fa-flag sobre fa-circle<br />
            <span className="fa-stack fa-lg">
              <i className="fa fa-square fa-stack-2x" />
              <i className="fa fa-terminal fa-stack-1x fa-inverse" />
            </span>
            fa-terminal sobre fa-square<br />
            <span className="fa-stack fa-lg">
              <i className="fa fa-camera fa-stack-1x" />
              <i className="fa fa-ban fa-stack-2x text-danger" />
            </span>
            fa-ban sobre fa-camera
          </div>
        </div>
      </div>
    </div>
  );
}
