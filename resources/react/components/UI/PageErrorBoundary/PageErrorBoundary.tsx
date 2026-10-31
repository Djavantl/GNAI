import { Component, type ErrorInfo, type ReactNode } from "react";
import Button from "@/components/UI/Button/Button";
import ErrorState from "@/components/UI/ErrorState/ErrorState";
import "./PageErrorBoundary.css";

const description = {
    title: "Não foi possível exibir esta tela",
    paragraphs: [
        "Ocorreu um erro inesperado nesta tela. Recarregue a página para continuar.",
    ],
};

/**
 * Isola falhas de renderização da página (por exemplo, de plugins jQuery) para
 * que o layout continue disponível em vez de a aplicação inteira ficar em branco.
 * O erro é limpo quando `resetKey` muda (por exemplo, a URL), sem remontar a
 * página nas visitas que preservam o estado, como filtros e paginação.
 */
export default class PageErrorBoundary extends Component<
    { children: ReactNode; homeHref: string; resetKey: string },
    { failed: boolean }
> {
    state = { failed: false };

    static getDerivedStateFromError(): { failed: boolean } {
        return { failed: true };
    }

    componentDidUpdate(previous: { resetKey: string }): void {
        if (this.state.failed && previous.resetKey !== this.props.resetKey) {
            this.setState({ failed: false });
        }
    }

    componentDidCatch(error: Error, info: ErrorInfo): void {
        console.error("Falha ao exibir a página.", error, info.componentStack);
    }

    render(): ReactNode {
        if (!this.state.failed) return this.props.children;

        return (
            <div className="pageErrorBoundary" role="alert">
                <ErrorState
                    code="500"
                    description={description}
                    homeHref={this.props.homeHref}
                />
                <p className="pageErrorBoundary__actions">
                    <Button
                        type="button"
                        tone="save"
                        leftIconClass="fa fa-refresh"
                        label="Recarregar página"
                        onClick={() => window.location.reload()}
                    />
                </p>
            </div>
        );
    }
}
