import { Fragment, useId } from "react";
import FormField from "@/Components/Forms/FormField/FormField";
import TextInput from "@/Components/Forms/TextInput/TextInput";
import TextArea from "@/Components/Forms/TextArea/TextArea";
import SelectField from "@/Components/Forms/SelectField/SelectField";
import ChoiceControl from "@/Components/Forms/ChoiceControl/ChoiceControl";
import ProductFormSection from "@/Components/Forms/ProductFormSection/ProductFormSection";
import {
  generalFields,
  categoryGroups,
  productStatuses,
  initialProductStatus,
} from "./ProductGeneralForm.data";
import "./ProductGeneralForm.css";
export default function ProductGeneralForm(): JSX.Element {
  const id = useId();
  return (
    <ProductFormSection title="Geral">
      {generalFields.map((field) => (
        <FormField
          key={field.name}
          id={`${id}-${field.name}`}
          label={field.label}
          requiredIndicator={field.requiredIndicator}
        >
          {field.rows ? (
            <TextArea
              id={`${id}-${field.name}`}
              name={field.name}
              rows={field.rows}
              placeholder={field.placeholder}
            />
          ) : (
            <TextInput
              id={`${id}-${field.name}`}
              name={field.name}
              placeholder={field.placeholder}
            />
          )}
        </FormField>
      ))}
      <FormField id={`${id}-categories`} label="Categorias" requiredIndicator>
        <SelectField
          id={`${id}-categories`}
          name="category"
          label="Categorias"
          groups={categoryGroups}
        />
      </FormField>
      <FormField id={`${id}-price`} label="Preço" requiredIndicator>
        <TextInput id={`${id}-price`} name="price" defaultValue="$ 562.56" />
      </FormField>
      <div className="productGeneralForm__status">
        <label>
          Status <span className="formField__required">*</span>
        </label>
        <br />
        {productStatuses.map((status, index) => (
          <Fragment key={status}>
            {index > 0 ? " " : null}
            <ChoiceControl
              type="radio"
              id={`${id}-status-${index}`}
              name={`${id}-status`}
              label={status}
              checked={status === initialProductStatus}
              inline
            />
          </Fragment>
        ))}
      </div>
      <FormField
        id={`${id}-comment`}
        label="Comentário"
        className="productGeneralForm__comment"
      >
        <TextArea
          id={`${id}-comment`}
          name="comment"
          rows={3}
          placeholder="Digite o resumo"
        />
      </FormField>
    </ProductFormSection>
  );
}
