
//
export const main = async props => {
  const page = await import(`~/?js_route=sample.${props.page ?? 1}`);
  const newProps = Object.assign(props, page.main ? await page.main(props) : {});
  const Page = page.default;
  const root = ReactDOM.createRoot(document.getElementById("app"));
  root.render(React.createElement(Page, newProps));
}

// htm is JSX-like syntax in plain JavaScript - no transpiler necessary.
// https://github.com/developit/htm
window.html = htm.bind(React.createElement);

// Material is a framework for sleek, responsive web interfaces.
// https://mui.com/
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

// clsx is a utility for constructing className strings conditionally.
// https://github.com/lukeed/clsx
//
//   You can use `clsx` instead of `cx` for this purpose.
//   https://emotion.sh/docs/@emotion/css#cx
