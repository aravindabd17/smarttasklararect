import { Link } from "react-router-dom";

const Sidebar = () => {
  return (
    <aside className="d-flex flex-column bg-dark pt-5">
        <button className="btn">
            <Link to="/">Home</Link>
        </button>
        <button className="btn btn-default">
            <Link to="/task">Task</Link>
        </button>
        <button className="btn btn-default">
            <Link to="/user">User</Link>
        </button>
    </aside>
  )
}

export default Sidebar;