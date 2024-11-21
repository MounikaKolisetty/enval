import { Component } from '@angular/core';
import { NavbarComponent } from '../navbar/navbar.component';
import { FooterComponent } from '../footer/footer.component';
import { HomeComponent } from '../home/home.component';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { ScrollButtonComponent } from '../scroll-button/scroll-button.component';

@Component({
  selector: 'app-contact',
  standalone: true,
  imports: [NavbarComponent,
            FooterComponent,
            HomeComponent,
            RouterOutlet, RouterLink, RouterLinkActive,
            ScrollButtonComponent
  ],
  templateUrl: './contact.component.html',
  styleUrl: './contact.component.css'
})
export class ContactComponent {

}
