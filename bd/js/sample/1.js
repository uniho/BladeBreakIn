
//
export const main = async props => {
  return {page1: 'ok!'};
}

//
export default props => {
  return html`
    <${Sx.flexCol} sx=${{my: 8, mx: 16}}>
      <div>
        Welcome to World of React and MUI on Blade!
      </div>
      <${Inner}>
        <${MUI.Button} variant="contained" sx=${{ml: 4}}>
          Contained
        <//>
        <${Div1}>
          Div!Div!
        <//>
        <${Span1} myColor="skyblue">
          Span!Span!
        <//>
      <//>
    <//>
  `;
}

//
const Inner = styled(Sx.flex)`
  padding-top: 2rem;
  align-items: center;
`;

//
const Div1 = styled('div')({
  marginLeft: '2rem',
});

//
const Span1 = styled('span')(props => {
  return css`
    color: ${props.myColor};
    margin-left: 2rem;
  `;
});
