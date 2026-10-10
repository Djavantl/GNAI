import { useRef } from 'react';
import CardBox from '@/Components/UI/CardBox/CardBox';
import {
  Col,
  Description,
  PageTitle,
  Row,
  SectionTitle
} from './ComponentsNestable.sections';
import { listOne, listThree, listTwo } from './ComponentsNestable.data';
import NestableList from './NestableList';
import type { NestablePluginHandle } from './useNestablePlugin';
import './ComponentsNestable.css';

export default function ComponentsNestable(): JSX.Element {
  const listOneRef = useRef<NestablePluginHandle | null>(null);
  const listTwoRef = useRef<NestablePluginHandle | null>(null);
  const listThreeRef = useRef<NestablePluginHandle | null>(null);
  const nestableRefs = [listOneRef, listTwoRef, listThreeRef];

  function expandAll(): void {
    nestableRefs.forEach((ref) => ref.current?.expandAll());
  }

  function collapseAll(): void {
    nestableRefs.forEach((ref) => ref.current?.collapseAll());
  }

  return (
    <section className="componentsNestablePage">
      <div className="componentsNestablePage__container">
        <PageTitle />
        <Row>
          <Col>
            <div className="text-left componentsNestablePage__menu" id="nestable_list_menu">
              <button type="button" className="btn btn-pink waves-effect waves-light" data-action="expand-all" onClick={expandAll}>Expandir tudo</button>
              <button type="button" className="btn btn-purple waves-effect waves-light" data-action="collapse-all" onClick={collapseAll}>Recolher tudo</button>
            </div>
          </Col>
        </Row>
        <br />
        <Row>
          <Col>
            <CardBox>
              <Row>
                <Col half>
                  <SectionTitle>Listas aninháveis 1</SectionTitle>
                  <Description />
                  <NestableList ref={listOneRef} group={1} id="nestable_list_1" items={listOne} />
                </Col>
                <Col half>
                  <SectionTitle>Listas aninháveis 2</SectionTitle>
                  <Description />
                  <NestableList ref={listTwoRef} group={1} id="nestable_list_2" items={listTwo} />
                </Col>
              </Row>
            </CardBox>
          </Col>
        </Row>
        <Row>
          <Col>
            <CardBox>
              <Row>
                <Col half>
                  <SectionTitle>Listas aninháveis 3</SectionTitle>
                  <Description />
                  <NestableList ref={listThreeRef} id="nestable_list_3" items={listThree} variant="empty" />
                </Col>
              </Row>
            </CardBox>
          </Col>
        </Row>
      </div>
    </section>
  );
}
