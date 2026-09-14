import { fireEvent, render, screen } from "@testing-library/react";
import axios from "axios";
import { beforeEach, describe, expect, vi } from "vitest";
import Users from "./Users";
import userEvent from "@testing-library/user-event";

vi.mock('axios');
vi.mock('react-hot-toast',()=>({
    default:{
        success:vi.fn() ,
        error:vi.fn()
    }
}))

const mockupData=[
    {
        id:1,
        name:"Aravind",
        username:"aravindbr94",
        email:"aravindbr94@gmail.com",
        address:{
            city:"Bangalore"
        },
    },
    {
        id: 2,
        name: 'Rahul Kumar',
        username: 'rahul123',
        email: 'rahul@gmail.com',
        address: {
            city: 'Chennai',
        },
    },
    {
        id: 3,
        name: 'John Smith',
        username: 'john123',
        email: 'john@gmail.com',
        address: {
        city: 'Covai',
        },
    },
    {
        id: 4,
        name: 'Priya Kumar',
        username: 'priya123',
        email: 'priya@gmail.com',
        address: {
        city: 'Covai',
        },
    },
    {
        id: 5,
        name: 'Vijay Kumar',
        username: 'vijay123',
        email: 'vijay@gmail.com',
        address: {
        city: 'Chennai',
        },
    },
    {
        id: 6,
        name: 'Suresh Kumar',
        username: 'suresh123',
        email: 'suresh@gmail.com',
        address: {
        city: 'Mumbai',
        },
    },
    ,
    {
        id: 7,
        name: 'Vignesh Kumar',
        username: 'suresh123',
        email: 'suresh@gmail.com',
        address: {
        city: 'Mumbai',
        },
    },
];
describe("user component loads",async()=>{
    beforeEach(()=>{
        vi.clearAllMocks();
        axios.get.mockResolvedValue({
            data:mockupData
        });
    });

    it("fetch and display the users",async()=>{
        render(<Users/>);
        expect(axios.get).toHaveBeenCalledWith('https://jsonplaceholder.typicode.com/users');
        expect(await screen.findByText("Aravind")).toBeInTheDocument();
        expect(screen.getByText("aravindbr94")).toBeInTheDocument();
        expect(screen.getByText("aravindbr94@gmail.com")).toBeInTheDocument();
        expect(screen.getByText("Bangalore")).toBeInTheDocument();
    });

    // Pagination
    it("pagination show first 5 users",async()=>{
        render(<Users/>);

        expect(await screen.findByText("Aravind"));

        expect(screen.getByText("Rahul Kumar")).toBeInTheDocument();
        expect(screen.getByText("John Smith")).toBeInTheDocument();
        expect(screen.getByText("Vijay Kumar")).toBeInTheDocument();
        expect(screen.getByText("Priya Kumar")).toBeInTheDocument();
        expect(screen.queryByText("Nagesh")).not.toBeInTheDocument();
    });

    // Next Page
    it("move to the next page",async()=>{
        render(<Users/>);

        expect(await screen.findByText("Aravind"));

        const nextButton=screen.getByRole("button",{
            name:"Next"
        });
        fireEvent.click(nextButton);
       expect(await screen.findByText("Vignesh Kumar")).toBeInTheDocument();
       expect(screen.queryByText("Aravind Kumar")).not.toBeInTheDocument();

    });

    it("search user by name",async()=>{
        const event=userEvent.setup();
        render(<Users/>);
        await screen.findByText("Aravind");
        const search=screen.getByPlaceholderText("Enter the search");
        await event.type(search,"Aravind");
        expect(screen.getByText("Aravind")).toBeInTheDocument();
        expect(screen.queryByText("Rahul kumar")).not.toBeInTheDocument();
    });

    it("open the user modal",async()=>{
        const event=userEvent.setup();
        render(<Users/>);
        await screen.findByText("Aravind");
        const button=screen.getByRole("button",{
            name:/add user/i
        });
        await event.click(button);
        expect(screen.getByText("Add New User")).toBeInTheDocument();
        expect(screen.getByText("Submit")).toBeInTheDocument();
    });

    it("open edit modal to open user data",async()=>{
        const event = userEvent.setup();
        render(<Users/>);
        await screen.findByText("Aravind");
        const editButton=screen.getAllByRole("button",{
            name:"Edit"
        });
        await event.click(editButton[0]);
        expect(screen.getByText("Edit User")).toBeInTheDocument();
        expect(screen.getByDisplayValue("Aravind")).toBeInTheDocument();
        expect(screen.getByDisplayValue("aravindbr94")).toBeInTheDocument();
        expect(screen.getByDisplayValue("aravindbr94@gmail.com")).toBeInTheDocument();
        expect(screen.getByDisplayValue("Bangalore")).toBeInTheDocument();
    });

    it("user has change his name",async()=>{
        const event=userEvent.setup();
        render(<Users/>);
        await screen.findByText("Aravind");

        const edit=screen.getAllByRole("button",{
            name:"Edit"
        });
        await event.click(edit[0]);

        const nameInput=screen.getByLabelText("Name");
        await event.clear(nameInput);
        await event.type(nameInput,"Joseph");
        expect(nameInput).toHaveValue("Joseph");
    });

    it("user has change his city",async()=>{
        const event=userEvent.setup();
        render(<Users/>);
        await screen.findByText("Aravind");

        const edit=screen.getAllByRole("button",{
            name:"Edit"
        });
        await event.click(edit[0]);

        const cityInput=screen.getByLabelText("City");
        await event.selectOptions(cityInput,"Bangalore");
        expect(cityInput).toHaveValue("Bangalore");
    });
});