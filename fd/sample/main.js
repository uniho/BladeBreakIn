
//
export const main = async props => {
  const page = await import(`~/?js_route=sample.${props.page ?? 1}`);
  const newProps = Object.assign(props, page.main ? await page.main() : {});
  const Page = page.default;
  const root = ReactDOM.createRoot(document.getElementById("app"));
  root.render(React.createElement(Page, newProps));
}

//
window.html = htm.bind(React.createElement);
window.MUI = MaterialUI;
window.styled = MaterialUI.styled;
window.css = MaterialUI.css;
window.keyframes = MaterialUI.keyframes;

// Define useful Sx Components
window.Sx = {
  div: MaterialUI.styled('div')({}),
  span: MaterialUI.styled('span')({}),
  flex: MaterialUI.styled('div')({display:'flex'}),
  flexCol: MaterialUI.styled('div')({display:'flex', flexDirection:'column'}),
};
